<?php

namespace Z38\SwissPayment;

/**
 * This class holds a structured representation of a postal address
 */
class StructuredPostalAddress implements PostalAddressInterface
{
    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $buildingNo;

    /**
     * @var string
     */
    protected $postCode;

    /**
     * @var string
     */
    protected $town;

    /**
     * @var string
     */
    protected $country;

    /**
     * Constructor
     *
     * @param string|null $street     Street name or null
     * @param string|null $buildingNo Building number or null
     * @param string      $postCode   Postal code
     * @param string      $town       Town name
     * @param string      $country    Country code (ISO 3166-1 alpha-2)
     */
    public function __construct($street, $buildingNo, $postCode, $town, $country = 'CH')
    {
        $this->street = (string) $street;
        $this->buildingNo = (string) $buildingNo;
        $this->postCode = (string) $postCode;
        $this->town = (string) $town;
        $this->country = (string) $country;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        if (strlen($this->street)) {
            $root->appendChild($doc->createElement('StrtNm', $this->street));
        }
        if (strlen($this->buildingNo)) {
            $root->appendChild($doc->createElement('BldgNb', $this->buildingNo));
        }
        $root->appendChild($doc->createElement('PstCd', $this->postCode));
        $root->appendChild($doc->createElement('TwnNm', $this->town));
        $root->appendChild($doc->createElement('Ctry', $this->country));

        return $root;
    }
}
