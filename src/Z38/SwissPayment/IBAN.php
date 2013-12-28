<?php

namespace Z38\SwissPayment;

/**
 * IBAN
 */
class IBAN
{
    const PATTERN = '/^[A-Z]{2,2}[0-9]{2,2}[A-Z0-9]{1,30}$/';

    protected $iban;

    /**
     * Constructor
     *
     * @param string $iban
     *
     * @throws \InvalidArgumentException When the IBAN does contain invalid characters or the checksum calculation fails.
     */
    public function __construct($iban)
    {
        $cleanedIban = str_replace(' ', '', strtoupper($iban));
        if (!preg_match(self::PATTERN, $cleanedIban)) {
            throw new \InvalidArgumentException('IBAN is not properly formatted.');
        }
        if (!self::check($cleanedIban)) {
            throw new \InvalidArgumentException('IBAN has an invalid checksum.');
        }

        $this->iban = $cleanedIban;
    }

    /**
     * Format the IBAN either in a human or machine-readable format
     *
     * @param bool $human Whether to group the IBAN in blocks of four characters
     *
     * @return string The formatted IBAN
     */
    public function format($human = true)
    {
        if ($human) {
            $parts = str_split($iban, 4);

            return implode(' ', $parts);
        } else {
            return $this->iban;
        }
    }

    /**
     * Gets the country
     *
     * @return string A ISO 3166-1 alpha-2 country code
     */
    public function getCountry()
    {
        return substr($this->iban, 0, 2);
    }

    /**
     * Checks whether the checksum of an IBAN is correct
     *
     * @param string $iban
     *
     * @return bool true if checksum is correct, false otherwise
     */
    protected static function check($iban)
    {
        $prepared = str_split(substr($iban, 4).substr($iban, 0, 4));
        foreach ($prepared as &$c) {
            if (ord($c) >= ord('A') && ord($c) <= ord('Z')) {
                $c = ord($c) - ord('A') + 10;
            }
        }
        unset($c);
        $prepared = implode($prepared);

        $r = '';
        $i = 0;
        while ($i < strlen($prepared)) {
            $d = $r.substr($prepared, $i, 9 - strlen($r));
            $i += 9 - strlen($r);
            $r = $d % 97;
        }

        return ($r == 1);
    }
}
