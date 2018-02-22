<?php

namespace Z38\SwissPayment\RemittanceInformation;

/**
 * RemittanceInformation
 */
interface RemittanceInformation
{
    /**
     * Returns a XML representation of the information
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM element
     */
    public function asDom(\DOMDocument $doc);
}
