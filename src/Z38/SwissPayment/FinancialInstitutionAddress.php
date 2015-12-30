<?php

namespace Z38\SwissPayment;

/**
 * FinancialInstitutionAddress holds information to identify a FI by name and address
 */
class FinancialInstitutionAddress implements FinancialInstitutionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var PostalAddressInterface
     */
    protected $address;

    /**
     * Constructor
     *
     * @param string $name Name of the FI
     * @param PostalAddressInterface Address of the FI
     */
    public function __construct($name, PostalAddressInterface $address)
    {
        $this->name = (string) $name;
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $xml = $doc->createElement('FinInstnId');
        $xml->appendChild($doc->createElement('Nm', $this->name));
        $xml->appendChild($this->address->asDom($doc));

        return $xml;
    }
}
