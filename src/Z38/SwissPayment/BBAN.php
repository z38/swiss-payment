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
    protected $accountNumber;

    /**
     * Constructor
     *
     * @param int $accountNumber
     *
     */
    public function __construct($accountNumber)
    {
        if (!self::check($accountNumber, 12)) {
            throw new InvalidArgumentException('Account number not valid.');
        }
        $this->accountNumber = $accountNumber;
    }

    public function format()
    {
        return $this->accountNumber;
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
