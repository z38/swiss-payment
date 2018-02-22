<?php

namespace Z38\SwissPayment\RemittanceInformation;

use Z38\SwissPayment\QRReference;

/**
 * QRReferenceInformation
 */
class QRReferenceInformation implements RemittanceInformation
{
    /**
     * @var QRReference
     */
    protected $reference;

    /**
     * @var string|null
     */
    protected $additionalInformation;

    /**
     * @param QRReference $reference
     * @param string|null $additionalInformation
     */
    public function __construct(QRReference $reference, $additionalInformation)
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
        $structured->appendChild($referenceInformation);

        $type = $doc->createElement('Tp');
        $code = $doc->createElement('CdOrPrtry');
        $code->appendChild($doc->createElement('Prtry', 'QRR'));
        $type->appendChild($code);
        $referenceInformation->appendChild($type);

        $reference = $doc->createElement('Ref', $this->reference->format());
        $referenceInformation->appendChild($reference);

        $structured->appendChild($referenceInformation);

        if (strlen($this->additionalInformation)) {
            $additionalInformation = $doc->createElement('AddtlRmtInf', $this->additionalInformation);
            $structured->appendChild($additionalInformation);
        }

        return $structured;
    }
}
