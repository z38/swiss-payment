<?php

namespace Z38\SwissPayment\RemittanceInformation;

/**
 * UnstructuredInformation
 */
class UnstructuredInformation implements RemittanceInformation
{
    /**
     * @var string
     */
    protected $information;

    /**
     * @param string $information
     */
    public function __construct($information)
    {
        $this->information = $information;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(\DOMDocument $doc)
    {
        return $doc->createElement('Ustrd', $this->information);
    }
}
