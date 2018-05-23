<?php
/**
 * Created by PhpStorm.
 * User: celinederoland
 * Date: 23.05.18
 * Time: 11:40
 */

namespace Z38\SwissPayment;

class EmptyPostalAddress implements PostalAddressInterface {

    /**
     * Returns a XML representation of the address
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM element
     */
    public function asDom(\DOMDocument $doc) {
        
        return null;
    }

}