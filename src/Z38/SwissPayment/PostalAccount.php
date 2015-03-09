<?php

namespace Z38\SwissPayment;

/**
 * PostalAccount holds details about a PostFinance account
 */
class PostalAccount
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
     * @throws \InvalidArgumentException When the account number is not valid (check digit is not being tested).
     */
    public function __construct($postalAccount)
    {
        if (!preg_match(self::PATTERN, $postalAccount)) {
            throw new \InvalidArgumentException('Postal account number is not properly formatted.');
        }

        $parts = explode('-', $postalAccount);
        if (!self::checkPrefix($parts[0])) {
            throw new \InvalidArgumentException('Postal account number has an invalid prefix.');
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
        return sprintf('%d-%d-%d', $this->prefix, $this->number, $this->checkDigit);
    }

    /**
     * Checks whether a given prefix is valid
     *
     * @param int $prefix The prefix to be checked
     *
     * @return bool True if the prefix is valid
     */
    private static function checkPrefix($prefix)
    {
        return in_array($prefix, array(
            10, 12, 17, 18, 19,
            20, 23, 25, 30, 34,
            40, 45, 46, 49, 50,
            60, 65, 69, 70, 80,
            82, 84, 85, 87, 90,
            91, 92
        ));
    }
}
