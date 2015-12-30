<?php

namespace Z38\SwissPayment;

/**
 * This class holds a unstructured representation of a postal address
 */
class UnstructuredPostalAddress implements PostalAddressInterface
{
    /**
     * @var array
     */
    protected $addressLines;

    /**
     * @var string
     */
    protected $country;

    /**
     * Constructor
     *
     * @param string $addressLine1 Street name and house number
     * @param string $addressLine2 Postcode and town
     * @param string $country      Country code (ISO 3166-1 alpha-2)
     */
    public function __construct($addressLine1 = null, $addressLine2 = null, $country = 'CH')
    {
        $this->addressLines = array();
        if ($addressLine1 !== null) {
            $this->addressLines[] = $addressLine1;
        }
        if ($addressLine2 !== null) {
            $this->addressLines[] = $addressLine2;
        }
        $this->country = (string) $country;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        $root->appendChild($doc->createElement('Ctry', $this->country));
        foreach ($this->addressLines as $line) {
            $root->appendChild($doc->createElement('AdrLine', $line));
        }

        return $root;
    }
}
