<?php

namespace Z38\SwissPayment;

/**
 * BC holds a Swiss bank clearing number
 */
class BC implements FinancialInstitutionInterface
{
    const PATTERN = '/^[0-9]{3,5}$/';

    /**
     * @var int
     */
    protected $bc;

    /**
     * Constructor
     *
     * @param string $bc
     *
     * @throws \InvalidArgumentException When the BC does contain invalid characters or the length does not match.
     */
    public function __construct($bc)
    {
        $bc = (string) $bc;
        if (!preg_match(self::PATTERN, $bc)) {
            throw new \InvalidArgumentException('BC is not properly formatted.');
        }

        $this->bc = (int) ltrim($bc, '0');
    }

    /**
     * Extracts the BC from an IBAN
     *
     * @param IBAN $iban
     *
     * @throws \InvalidArgumentException When the supplied IBAN is not from Switzerland
     */
    public static function fromIBAN(IBAN $iban)
    {
        if ($iban->getCountry() !== 'CH') {
            throw new \InvalidArgumentException('BC can only be extracted from Swiss IBANs.');
        }

        return new self(substr($iban->normalize(), 4, 5));
    }

    /**
     * Returns a formatted representation of the BIC
     *
     * @return string The formatted BIC
     */
    public function format()
    {
        return sprintf('%d', $this->bc);
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $xml = $doc->createElement('FinInstnId');
        $clearingSystem = $doc->createElement('ClrSysMmbId');
        $clearingSystemId = $doc->createElement('ClrSysId');
        $clearingSystemId->appendChild($doc->createElement('Cd', 'CHBCC'));
        $clearingSystem->appendChild($clearingSystemId);
        $clearingSystem->appendChild($doc->createElement('MmbId', $this->format()));
        $xml->appendChild($clearingSystem);

        return $xml;
    }
}
