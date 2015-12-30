<?php

namespace Z38\SwissPayment;


interface PostalAddressInterface
{
    public function asDom(\DOMDocument $doc);
}
