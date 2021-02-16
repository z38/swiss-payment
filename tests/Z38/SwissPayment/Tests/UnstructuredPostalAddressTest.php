<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\UnstructuredPostalAddress;

/**
 * @coversDefaultClass \Z38\SwissPayment\UnstructuredPostalAddress
 */
class UnstructuredPostalAddressTest extends TestCase
{
    /**
     * @covers ::sanitize
     */
    public function testSanitize()
    {
        self::assertInstanceOf('Z38\SwissPayment\UnstructuredPostalAddress', UnstructuredPostalAddress::sanitize(
            "Dorf—Strasse 3\n\n",
            "8000\tZürich"
        ));
    }
}
