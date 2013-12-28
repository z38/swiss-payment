<?php

namespace Z38\SwissPayment;

/**
 * This class holds a structured representation of a postal address
 */
class PostalAddress
{
    protected $street;
    protected $buildingNo;
    protected $postCode;
    protected $town;
    protected $country;

    /**
     * Constructor
     *
     * @param string      $street     Street name
     * @param string|null $buildingNo Building number or null
     * @param string      $postCode   Postal code
     * @param string      $town       Town name
     * @param string      $country    Country code (ISO 3166-1 alpha-2)
     */
    public function __construct($street, $buildingNo, $postCode, $town, $country = 'CH')
    {
        $this->street = $street;
        $this->buildingNo = $buildingNo;
        $this->postCode = $postCode;
        $this->town = $town;
        $this->country = $country;
    }

    /**
     * Returns a DOM element
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        $root->appendChild($doc->createElement('StrtNm', $this->street));
        if (!empty($this->buildingNo)) {
            $root->appendChild($doc->createElement('BldgNb', $this->buildingNo));
        }
        $root->appendChild($doc->createElement('PstCd', $this->postCode));
        $root->appendChild($doc->createElement('TwnNm', $this->town));
        $root->appendChild($doc->createElement('Ctry', $this->country));

        return $root;
    }
}
