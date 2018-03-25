<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\StructuredPostalAddress;

/**
 * @coversDefaultClass \Z38\SwissPayment\StructuredPostalAddress
 */
class StructuredPostalAddressTest extends TestCase
{
    /**
     * @covers ::sanitize
     */
    public function testSanitize()
    {
        $this->assertInstanceOf('Z38\SwissPayment\StructuredPostalAddress', StructuredPostalAddress::sanitize(
            'Dorfstrasse',
            '∅',
            'Pfaffenschlag bei Waidhofen an der Thaya',
            '3834',
            'AT'
        ));
    }
}
