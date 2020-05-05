<?php

namespace Z38\SwissPayment;

use DOMDocument;
use DOMElement;

/**
 * PostalAddressInterface
 */
interface PostalAddressInterface
{
    /**
     * Returns a XML representation of the address
     *
     * @param DOMDocument $doc
     *
     * @return DOMElement The built DOM element
     */
    public function asDom(DOMDocument $doc);
}
