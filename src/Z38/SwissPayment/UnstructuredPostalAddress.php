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
     *
     * @throws \InvalidArgumentException When the address contains invalid characters or is too long.
     */
    public function __construct($addressLine1 = null, $addressLine2 = null, $country = 'CH')
    {
        $this->addressLines = [];
        if ($addressLine1 !== null) {
            $this->addressLines[] = Text::assert($addressLine1, 70);
        }
        if ($addressLine2 !== null) {
            $this->addressLines[] = Text::assert($addressLine2, 70);
        }
        $this->country = Text::assertCountryCode($country);
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $root = $doc->createElement('PstlAdr');

        $root->appendChild(Text::xml($doc, 'Ctry', $this->country));
        foreach ($this->addressLines as $line) {
            $root->appendChild(Text::xml($doc, 'AdrLine', $line));
        }

        return $root;
    }
}
