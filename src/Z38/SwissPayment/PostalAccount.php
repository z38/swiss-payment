<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * PostalAccount holds details about a PostFinance account
 */
class PostalAccount implements AccountInterface
{
    const PATTERN = '/^[0-9]{2}-[1-9][0-9]{0,5}-[0-9]$/';

    /**
     * @var int
     */
    protected $prefix;

    /**
     * @var int
     */
    protected $number;

    /**
     * @var int
     */
    protected $checkDigit;

    /**
     * Constructor
     *
     * @param string $postalAccount
     *
     * @throws InvalidArgumentException When the account number is not valid.
     */
    public function __construct($postalAccount)
    {
        if (!preg_match(self::PATTERN, $postalAccount)) {
            throw new InvalidArgumentException('Postal account number is not properly formatted.');
        }

        $parts = explode('-', $postalAccount);
        if (self::calculateCheckDigit(sprintf('%02s%06s', $parts[0], $parts[1])) !== (int) $parts[2]) {
            throw new InvalidArgumentException('Postal account number has an invalid check digit.');
        }

        $this->prefix = (int) $parts[0];
        $this->number = (int) $parts[1];
        $this->checkDigit = (int) $parts[2];
    }

    /**
     * Format the postal account number
     *
     * @return string The formatted account number
     */
    public function format()
    {
        return sprintf('%02d-%d-%d', $this->prefix, $this->number, $this->checkDigit);
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $root = $doc->createElement('Id');
        $other = $doc->createElement('Othr');
        $other->appendChild($doc->createElement('Id', $this->format()));
        $root->appendChild($other);

        return $root;
    }

    private static function calculateCheckDigit($number)
    {
        $lookup = array(0, 9, 4, 6, 8, 2, 7, 1, 3, 5);
        $carry = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $carry = $lookup[($carry + $number[$i]) % 10];
        }

        return (10 - $carry) % 10;
    }
}
