<?php

namespace Z38\SwissPayment;

/**
 * This class holds a unstructured representation of a postal address
 */
class UnstructuredPostalAddress implements PostalAddressInterface
{
    /**
     * @var string
     */
    protected $adrLine1;

    /**
     * @var string
     */
    protected $adrLine2;

    /**
     * @var string
     */
    protected $country;

    /**
     * Constructor
     *
     * @param string    $adrLine1   Street name and house number
     * @param string    $ardLine2   Postcode and town
     * @param string    $country    Country code (ISO 3166-1 alpha-2)
     */
    public function __construct($adrLine1 = null, $ardLine2, $country = 'CH')
    {
        $this->adrLine1 = $adrLine1;
        $this->adrLine2 = $ardLine2;
        $this->country = (string) $country;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        $root->appendChild($doc->createElement('Ctry', $this->country));
        if (null !== $this->adrLine1) {
            $root->appendChild($doc->createElement('AdrLine', $this->adrLine1));
        }
        $root->appendChild($doc->createElement('AdrLine', $this->adrLine2));

        return $root;
    }
}
