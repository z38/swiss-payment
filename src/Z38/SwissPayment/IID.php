<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * IID holds a Swiss institutional identification number (formerly known as BC number)
 */
class IID implements FinancialInstitutionInterface
{
    /**
     * @var string
     */
    protected $iid;

    /**
     * Constructor
     *
     * @param string $iid
     *
     * @throws \InvalidArgumentException When the IID does contain invalid characters or the length does not match.
     */
    public function __construct($iid)
    {
        $iid = (string) $iid;
        if (!preg_match('/^[0-9]{3,5}$/', $iid)) {
            throw new InvalidArgumentException('IID is not properly formatted.');
        }

        $this->iid = str_pad($iid, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Extracts the IID from an IBAN
     *
     * @param IBAN $iban
     *
     * @throws \InvalidArgumentException When the supplied IBAN is not from Switzerland
     */
    public static function fromIBAN(IBAN $iban)
    {
        if ($iban->getCountry() !== 'CH') {
            throw new InvalidArgumentException('IID can only be extracted from Swiss IBANs.');
        }

        return new self(substr($iban->normalize(), 4, 5));
    }

    /**
     * Returns a formatted representation of the IID
     *
     * @return string The formatted IID
     */
    public function format()
    {
        return $this->iid;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $xml = $doc->createElement('FinInstnId');
        $clearingSystem = $doc->createElement('ClrSysMmbId');
        $clearingSystemId = $doc->createElement('ClrSysId');
        $clearingSystemId->appendChild($doc->createElement('Cd', 'CHBCC'));
        $clearingSystem->appendChild($clearingSystemId);
        $clearingSystem->appendChild($doc->createElement('MmbId', ltrim($this->iid, '0'))); // strip zeroes for legacy systems
        $xml->appendChild($clearingSystem);

        return $xml;
    }
}
