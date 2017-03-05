<?php

namespace Z38\SwissPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;

/**
 * PurposeCode contains a purpose code from the External Code Sets
 */
class PurposeCode
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Constructor
     *
     * @param string $code
     *
     * @throws \InvalidArgumentException When the code is not valid
     */
    public function __construct($code)
    {
        $code = (string) $code;
        if (!preg_match('/^[A-Z0-9]{4}$/', $code)) {
            throw new InvalidArgumentException('The purpose code is not valid.');
        }

        $this->code = $code;
    }

    /**
     * Returns a XML representation of this purpose
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM element
     */
    public function asDom(DOMDocument $doc)
    {
        return $doc->createElement('Cd', $this->code);
    }
}
