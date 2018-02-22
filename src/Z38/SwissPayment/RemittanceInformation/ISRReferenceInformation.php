<?php

namespace Z38\SwissPayment\RemittanceInformation;

/**
 * ISRReferenceInformation
 */
class ISRReferenceInformation implements RemittanceInformation
{
    protected $reference;

    /**
     * @param string $reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        $structured = $doc->createElement('Strd');
        $referenceInformation = $doc->createElement('CdtrRefInf');
        $reference = $doc->createElement('Ref', $this->reference);
        $referenceInformation->appendChild($reference);
        $structured->appendChild($referenceInformation);

        return $structured;
    }
}
