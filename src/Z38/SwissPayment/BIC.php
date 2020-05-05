<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * BIC
 */
class BIC implements FinancialInstitutionInterface
{
    const PATTERN = '/^[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}$/';

    /**
     * @var string
     */
    protected $bic;

    /**
     * Constructor
     *
     * @param string $bic
     *
     * @throws InvalidArgumentException When the BIC does contain invalid characters or the length does not match.
     */
    public function __construct($bic)
    {
        if (!preg_match(self::PATTERN, $bic)) {
            throw new InvalidArgumentException('BIC is not properly formatted.');
        }

        $this->bic = $bic;
    }

    /**
     * Returns a formatted representation of the BIC
     *
     * @return string The formatted BIC
     */
    public function format()
    {
        return $this->bic;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $xml = $doc->createElement('FinInstnId');
        $xml->appendChild($doc->createElement('BIC', $this->format()));

        return $xml;
    }
}
