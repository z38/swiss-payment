<?php

namespace Z38\SwissPayment;

use InvalidArgumentException;

/**
 * QRCode contains the data of a Swiss QR Code.
 */
class QRCode
{
    /**
     * @var IBAN
     */
    protected $creditorAccount;

    /**
     * @var string
     */
    protected $creditorName;

    /**
     * @var StructuredPostalAddress
     */
    protected $creditorAddress;

    /**
     * @var string|null
     */
    protected $ultimateCreditorName;

    /**
     * @var StructuredPostalAddress|null
     */
    protected $ultimateCreditorAddress;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var Money\Money|null
     */
    protected $amount;

    /**
     * @var string|null
     */
    protected $dueDate;

    /**
     * @var string|null
     */
    protected $ultimateDebtorName;

    /**
     * @var StructuredPostalAddress|null
     */
    protected $ultimateDebtorAddress;

    /**
     * @var QRReference|CreditorReference|null
     */
    protected $reference;

    /**
     * @var string|null
     */
    protected $unstructuredMessage;

    /**
     * @var array
     */
    protected $alternativeSchemes;

    /**
     * Constructor
     *
     * @param string $code
     *
     * @throws \InvalidArgumentException When the QR code is malformed or otherwise invalid.
     */
    public function __construct($code)
    {
        $elements = explode("\r\n", $code);
        if (count($elements) < 28) {
            throw new InvalidArgumentException('QR code is malformed.');
        }
        if ($elements[0] !== 'SPC' || $elements[1] !== '0100' || $elements[2] !== '1') {
            throw new InvalidArgumentException('Unsupported version.');
        }

        $this->creditorAccount = new IBAN($elements[3]);
        if (!in_array($this->creditorAccount->getCountry(), ['CH', 'LI'])) {
            throw new \InvalidArgumentException('IBAN must be from Switzerland or Lichtenstein.');
        }

        list($this->creditorName, $this->creditorAddress) = $this->parseAddress(array_slice($elements, 4));
        if ($this->creditorName === null) {
            throw new InvalidArgumentException('Creditor is invalid.');
        }

        list($this->ultimateCreditorName, $this->ultimateCreditorAddress) = $this->parseAddress(array_slice($elements, 10));

        $this->currency = $elements[17];
        if ($this->currency !== 'CHF' && $this->currency !== 'EUR') {
            throw new InvalidArgumentException('Unsupported currency.');
        }

        if ($elements[16] !== '') {
            if (!preg_match('/^[0-9, ]+\.[0-9]{2}$/', $elements[16])) {
                throw new InvalidArgumentException('Amount is invalid.');
            }
            //$cents = intval(str_replace($elements[16], '.', ''));
            $cents = intval(preg_replace('/[^0-9]+/', '', $elements[16]));
            if ($this->currency === 'CHF') {
                $this->amount = new Money\CHF($cents);
            } else {
                $this->amount = new Money\EUR($cents);
            }
        }

        if ($elements[18] !== '') {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $elements[18])) {
                throw new InvalidArgumentException('Due date is invalid.');
            }
            $this->dueDate = $elements[18];
        }

        list($this->ultimateDebtorName, $this->ultimateDebtorAddress) = $this->parseAddress(array_slice($elements, 19));

        switch ($elements[25]) {
            case 'QRR':
                $this->reference = new QRReference($elements[26]);
                break;
            case 'SCOR':
                $this->reference = new CreditorReference($elements[26]);
                break;
            case 'NON':
                if ($elements[26] !== '') {
                    throw new InvalidArgumentException('Reference number is not allowed.');
                }
                break;
            default:
                throw new InvalidArgumentException('Unsupported reference type.');
        }

        $this->unstructuredMessage = strlen($elements[27]) ? $elements[27] : null;

        $this->alternativeSchemes = [];
        foreach (array_slice($elements, 28) as $alternativeScheme) {
            $this->alternativeSchemes[] = $alternativeScheme;
        }
    }

    /**
     * Gets the IBAN of the creditor
     *
     * @return IBAN
     */
    public function getCreditorAccount()
    {
        return $this->creditorAccount;
    }

    /**
     * Gets the name of the creditor
     *
     * @return string A ISO 3166-1 alpha-2 country code
     */
    public function getCreditorName()
    {
        return $this->creditorName;
    }

    /**
     * Gets the address of the creditor
     *
     * @return StructuredPostalAddress
     */
    public function getCreditorAddress()
    {
        return $this->creditorAddress;
    }

    /**
     * Gets the name of the ultimate creditor
     *
     * @return string|null
     */
    public function getUltimateCreditorName()
    {
        return $this->ultimateCreditorName;
    }

    /**
     * Gets the address of the ultimate creditor
     *
     * @return StructuredPostalAddress|null
     */
    public function getUltimateCreditorAddress()
    {
        return $this->ultimateCreditorAddress;
    }

    /**
     * Gets the currency of the creditor's account
     *
     * @return string A ISO 4217 currency code
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Gets the amount
     *
     * @return Money\Money|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Gets the due date
     *
     * @return string|null
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Gets the name of the ultimate debtor
     *
     * @return string|null
     */
    public function getUltimateDebtorName()
    {
        return $this->ultimateDebtorName;
    }

    /**
     * Gets the address of the ultimate debtor
     *
     * @return StructuredPostalAddress|null
     */
    public function getUltimateDebtorAddress()
    {
        return $this->ultimateDebtorAddress;
    }

    /**
     * Gets the reference number
     *
     * @return CreditorReference|QRReference|null
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Gets the additional information
     *
     * @return string|null
     */
    public function getUnstructuredMessage()
    {
        return $this->unstructuredMessage;
    }

    /**
     * Gets a list of subelements for a given identifier.
     *
     //* @param string $id The 2-char identifier
     * @param string $id The 3-char identifier
     *
     * @return array|null
     */
    public function getAlternativeScheme($id)
    {
        foreach ($this->alternativeSchemes as $scheme) {
            // if($id === substr($scheme, 0, 2) && isset($scheme[2])) {
            //     return explode($scheme[2], substr($scheme, 3));
            if ($id === substr($scheme, 0, 3) && isset($scheme[3])) {
                return explode($scheme[3], substr($scheme, 4));
            }
        }

        return null;
    }

    /**
     * Gets a list of all alternative schemes
     *
     * @return array
     */
    public function getAlternativeSchemes()
    {
        return $this->alternativeSchemes;
    }

    /**
     * Parses an address
     *
     * @param array $elements
     *
     * @returns StructuredPostalAddress|null
     *
     * @throws \InvalidArgumentException When the address is malformed.
     */
    protected function parseAddress(array $elements)
    {
        if (count($elements) < 6) {
            throw new InvalidArgumentException('Address is malformed.');
        }
        list($name, $street, $houseNumber, $postalCode, $city, $country) = $elements;
        $dependent = count(array_filter([$name, $postalCode, $city, $country], 'strlen'));
        if ($dependent === 0) {
            return [null, null];
        }
        if ($dependent !== 4) {
            throw new InvalidArgumentException('Address is incomplete.');
        }

        return [$name, new StructuredPostalAddress(
            $street,
            $houseNumber,
            $postalCode,
            $city,
            $country
        )];
    }
}
