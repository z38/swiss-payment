<?php

namespace Z38\SwissPayment\Tests;

use InvalidArgumentException;
use Z38\SwissPayment\IBAN;

class IBANTest extends TestCase
{
    /**
     * @param $iban
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
     * @param $iban
     * @param $expectedCountry
     * @dataProvider samplesValid
     * @covers \Z38\SwissPayment\IBAN::getCountry
     */
    public function testGetCountry($iban, $expectedCountry)
    {
        $instance = new IBAN($iban);
        $this->assertEquals($expectedCountry, $instance->getCountry());
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::format
     */
    public function testFormat()
    {
        $iban = new IBAN('ch9300762011623852 957');
        $this->assertEquals('CH93 0076 2011 6238 5295 7', $iban->format());
    }

    /**
     * @covers \Z38\SwissPayment\IBAN::normalize
     */
    public function testNormalize()
    {
        $iban = new IBAN('fr14 2004 10100505 0001 3M02 606');
        $this->assertEquals('FR1420041010050500013M02606', $iban->normalize());
    }

    /**
     * @depends      testFormat
     * @dataProvider samplesValid
     * @covers       \Z38\SwissPayment\IBAN::__toString
     * @param $iban
     */
    public function testToString($iban)
    {
        $instance = new IBAN($iban);
        $this->assertEquals($instance->format(), (string) $instance);
    }

    public function samplesValid()
    {
        return [
            ['AZ21 NABZ 0000 0000 1370 1000 1944', 'AZ'],
            ['FR14 2004 1010 0505 0001 3M02 606', 'FR'],
            ['ch930076201162385295 7', 'CH'],
        ];
    }

    /**
     * @param $iban
     * @param $valid
     */
    protected function check($iban, $valid)
    {
        $exception = false;
        try {
            new IBAN($iban);
        } catch (InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
