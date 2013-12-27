<?php

namespace Z38\SwissPayment;

class PostalAddress
{
    protected $street;
    protected $buildingNo;
    protected $postCode;
    protected $town;
    protected $country;

    public function __construct($street, $buildingNo, $postCode, $town, $country = 'CH')
    {
        $this->street = $street;
        $this->buildingNo = $buildingNo;
        $this->postCode = $postCode;
        $this->town = $town;
        $this->country = $country;
    }

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
