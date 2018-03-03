<?php

namespace Z38\SwissPayment;

/**
 * This class holds a structured representation of a postal address
 */
class StructuredPostalAddress implements PostalAddressInterface
{
    /**
     * @var string|null
     */
    protected $street;

    /**
     * @var string|null
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
     *
     * @throws \InvalidArgumentException When the address contains invalid characters or is too long.
     */
    public function __construct($street, $buildingNo, $postCode, $town, $country = 'CH')
    {
        $this->street = Text::assertOptional($street, 70);
        $this->buildingNo = Text::assertOptional($buildingNo, 16);
        $this->postCode = Text::assert($postCode, 16);
        $this->town = Text::assert($town, 35);
        $this->country = Text::assertCountryCode($country);
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        if ($this->street !== null) {
            $root->appendChild(Text::xml($doc, 'StrtNm', $this->street));
        }
        if ($this->buildingNo !== null) {
            $root->appendChild(Text::xml($doc, 'BldgNb', $this->buildingNo));
        }
        $root->appendChild(Text::xml($doc, 'PstCd', $this->postCode));
        $root->appendChild(Text::xml($doc, 'TwnNm', $this->town));
        $root->appendChild(Text::xml($doc, 'Ctry', $this->country));

        return $root;
    }
}
