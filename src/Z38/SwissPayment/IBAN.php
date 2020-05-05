<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * IBAN
 */
class IBAN implements AccountInterface
{
    const MAX_LENGTH = 34;
    const PATTERN = '/^[A-Z]{2,2}[0-9]{2,2}[A-Z0-9]{1,30}$/';

    /**
     * @var string
     */
    protected $iban;

    /**
     * Constructor
     *
     * @param string $iban
     *
     * @throws InvalidArgumentException When the IBAN does contain invalid characters or the checksum calculation fails.
     */
    public function __construct($iban)
    {
        $cleanedIban = str_replace(' ', '', strtoupper($iban));
        if (!preg_match(self::PATTERN, $cleanedIban)) {
            throw new InvalidArgumentException('IBAN is not properly formatted.');
        }
        if (!self::check($cleanedIban)) {
            throw new InvalidArgumentException('IBAN has an invalid checksum.');
        }

        $this->iban = $cleanedIban;
    }

    /**
     * Format the IBAN either in a human-readable manner
     *
     * @return string The formatted IBAN
     */
    public function format()
    {
        $parts = str_split($this->iban, 4);

        return implode(' ', $parts);
    }

    /**
     * Normalize the IBAN
     *
     * @return string The normalized IBAN
     */
    public function normalize()
    {
        return $this->iban;
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
        $chars = str_split(substr($iban, 4).substr($iban, 0, 4));
        $length = count($chars);
        for ($i = 0; $i < $length; $i++) {
            $code = ord($chars[$i]);
            if ($code >= 65 && $code <= 90) { // A-Z
                $chars[$i] = $code - 65 + 10;
            }
        }
        $prepared = implode($chars);

        $r = '';
        $rLength = 0;
        $i = 0;
        $length = strlen($prepared);
        while ($i < $length) {
            $d = $r.substr($prepared, $i, 9 - $rLength);
            $i += 9 - $rLength;
            $r = $d % 97;
            $rLength = 1 + ($r >= 10);
        }

        return ($r == 1);
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $xml = $doc->createElement('Id');
        $xml->appendChild($doc->createElement('IBAN', $this->normalize()));

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
