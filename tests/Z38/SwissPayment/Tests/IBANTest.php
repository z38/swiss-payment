<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\IBAN;

class IBANTest extends TestCase
{
    /**
     * @dataProvider samplesValid
     * @covers \Z38\SwissPayment\IBAN::__construct
     */
    public function testValid($iban)
    {
        $this->check($iban, true);
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::__construct
     */
    public function testInvalidChars()
    {
        $this->check('CZ28-0300-0080-1005-6650-1963', false);
        $this->check('CZ28:0300:0080:1005:6650:1963', false);
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::__construct
     */
    public function testWrongChecksum()
    {
        $this->check('FR13 2004 1010 0505 0001 3M02 606', false);
        $this->check('CH9200762011623852957', false);
    }

    /**
     * @dataProvider samplesValid
     * @covers \Z38\SwissPayment\IBAN::getCountry
     */
    public function testGetCountry($iban, $expectedCountry)
    {
        $instance = new IBAN($iban);
        self::assertEquals($expectedCountry, $instance->getCountry());
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::format
     */
    public function testFormat()
    {
        $iban = new IBAN('ch9300762011623852 957');
        self::assertEquals('CH93 0076 2011 6238 5295 7', $iban->format());
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::normalize
     */
    public function testNormalize()
    {
        $iban = new IBAN('fr14 2004 10100505 0001 3M02 606');
        self::assertEquals('FR1420041010050500013M02606', $iban->normalize());
    }

    /**
     * @depends testFormat
     * @dataProvider samplesValid
     * @covers \Z38\SwissPayment\IBAN::__toString
     */
    public function testToString($iban)
    {
        $instance = new IBAN($iban);
        self::assertEquals($instance->format(), (string) $instance);
    }

    public function samplesValid()
    {
        return [
            ['AZ21 NABZ 0000 0000 1370 1000 1944', 'AZ'],
            ['FR14 2004 1010 0505 0001 3M02 606', 'FR'],
            ['ch930076201162385295 7', 'CH'],
        ];
    }

    protected function check($iban, $valid)
    {
        $exception = false;
        try {
            $temp = new IBAN($iban);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        self::assertTrue($exception != $valid);
    }
}
