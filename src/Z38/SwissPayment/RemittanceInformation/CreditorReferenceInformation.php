<?php

namespace Z38\SwissPayment\RemittanceInformation;

use Z38\SwissPayment\CreditorReference;

/**
 * CreditorReference
 */
class CreditorReferenceInformation implements RemittanceInformation
{
    protected $reference;

    /**
     * @param CreditorReference $reference
     */
    public function __construct(CreditorReference $reference)
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

        $type = $doc->createElement('Tp');
        $code = $doc->createElement('CdOrPrtry');
        $code->appendChild($doc->createElement('Cd', 'SCOR'));
        $type->appendChild($code);
        $referenceInformation->appendChild($type);

        $reference = $doc->createElement('Ref', $this->reference->format());
        $referenceInformation->appendChild($reference);

        $structured->appendChild($referenceInformation);

        return $structured;
    }
}
