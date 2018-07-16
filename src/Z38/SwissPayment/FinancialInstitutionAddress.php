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
     * @param string                 $name    Name of the FI
     * @param PostalAddressInterface $address Address of the FI
     *
     * @throws \InvalidArgumentException When the name is invalid.
     */
    public function __construct($name, PostalAddressInterface $address)
    {
        $this->name = Text::assert($name, 70);
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $xml = $doc->createElement('FinInstnId');
        $xml->appendChild(Text::xml($doc, 'Nm', $this->name));
        $xml->appendChild($this->address->asDom($doc));

        return $xml;
    }
}
