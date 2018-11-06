<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * BBAN

 * @see
 * https://www.nordea.com/Images/34-48937/Nordea_Account_Structure_v1_4.pdf
 * for documentation.
 */
class BBAN implements AccountInterface
{
    /**
     * @var string
     */
    protected $registrationNumber;
    protected $accountNumber;

    /**
     * Constructor
     *
     * @param int $registrationNumber
     * @param int $accountNumber
     *
     */
    public function __construct($registrationNumber, $accountNumber)
    {
        if (!self::check($registrationNumber, 4)) {
            throw new InvalidArgumentException('Bank registration number not valid.');
        }
        if (!self::check($accountNumber, 10)) {
            throw new InvalidArgumentException('Account number not valid.');
        }
        $this->accountNumber = $accountNumber;
        $this->registrationNumber = $registrationNumber;
    }

    /**
     * Format the BBAN according to Nordea requirements.
     *
     * Bank account number must be 14 digits. The first 4 digits of the bank
     * account number must be the bank registration number. The last 10 digits
     * must be the account number. If the bank registration number is
     * shorter than 4 digits or the account number is shorter than 10 digits, it
     * must be right aligned and padded with leading zeroes.
     *
     * @return string The formatted BBAN
     */
    public function format()
    {
        $registrationNumber = str_pad($this->registrationNumber, 4, '0', STR_PAD_LEFT);
        $accountNumber = str_pad($this->accountNumber, 10, '0', STR_PAD_LEFT);
        return $registrationNumber . $accountNumber;
    }

    /**
     * Normalize the BBAN
     *
     * @return string The normalized BBAN
     */
    public function normalize()
    {
        return $this->bban;
    }

    /**
     * Basic checks.
     *
     * @param string $number
     * @param integer $max
     *
     * @return bool
     */
    protected static function check($number, $max = 0)
    {
        if (!is_numeric($number)) {
            return false;
        }
        if ($max && $max < strlen($number)) {
            return false;
        }
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {

        $code = $doc->createElement('Cd', 'BBAN');
        $schem = $doc->createElement('SchmeNm');
        $schem->appendChild($code);
        $id = $doc->createElement('Id', $this->format());
        $other = $doc->createElement('Othr');
        $other->appendChild($id);
        $other->appendChild($schem);
        $xml = $doc->createElement('Id');
        $xml->appendChild($other);

        return $xml;
    }

    /**
     * Returns a string representation.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return $this->format();
    }
}
