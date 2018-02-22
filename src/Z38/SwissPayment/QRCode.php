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
     * @var Money|null
     */
    protected $amount;

    /**
     * @var string|null
     */
    protected $dueDate;

    /**
     * @var string|null
     */
    protected $ultimateDebitorName;

    /**
     * @var StructuredPostalAddress|null
     */
    protected $ultimateDebitorAddress;

    /**
     * @var string
     */
    protected $referenceType;

    /**
     * @var string|null
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
        if (count($elements) < 3 + 1 + 6 + 6 + 3 + 6 + 3) {
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
        if ($this->currency !== 'CHF' || $this->currency !== 'EUR') {
            throw new InvalidArgumentException('Unsupported currency.');
        }

        if ($elements[16] !== '') {
            if (!preg_match('/^\d+\.\d{2}$', $elements[16])) {
                throw new InvalidArgumentException('Amount is invalid.');
            }
            $cents = intval(str_replace($elements[16], '.', ''));
            if ($this->currency === 'CHF') {
                $this->amount = new Money\CHF($cents);
            } else {
                $this->amount = new Money\EUR($cents);
            }
        }

        if ($elements[17] !== '') {
            if (!preg_match('^\d{4}-\d{2}-\d{2}$', $elements[17])) {
                throw new InvalidArgumentException('Due date is invalid.');
            }
            $this->dueDate = $elements[17];
        }

        list($this->ultimateDebitorName, $this->ultimateDebitorAddress) = $this->parseAddress(array_slice($elements, 18));

        $this->referenceType = $elements[24];
        $this->reference = $elements[25];
        switch($this->referenceType) {
            case 'QRR':
                if(!preg_match('^\d{27}$', $this->reference)) {
                    throw new InvalidArgumentException('Invalid QR reference.');
                }
            break;
            case 'SCOR':
                if(!preg_match('^RF[0-9]{2}[0-9A-Z]{0,21}$', $this->reference)) {
                    throw new InvalidArgumentException('Invalid creditor reference.');
                }
            break;
            case 'NON':
                if ($this->reference !== '') {
                    throw new InvalidArgumentException('Reference number is not allowed.');
                }
                $this->reference = null;
            break;
            default:
                throw new InvalidArgumentException('Unsupported reference type.');
        }
        $this->unstructuredMessage = strlen($elements[26]) ? $elements[26] : null;

        $this->alternativeSchemes = [];
        foreach(array_slice($elements, 27) as $alternativeScheme) {
            $this->alternativeSchemes[] = $alternativeScheme;
        }
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
        if(count($elements) < 6) {
            throw new InvalidArgumentException('Address is malformed.');
        }
        list($name, $street, $houseNumber, $postalCode, $city, $country) = $elements;
        $dependent = array_filter('strlen', [$name, $postalCode, $city, $country]);
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
