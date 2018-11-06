<?php

namespace NordeaPayment\Tests;

use NordeaPayment\BBAN;

class BBANTest extends TestCase
{
    /**
     * @dataProvider samplesValid
     * @covers \NordeaPayment\BBAN::__construct
     */
    public function testValid($bbanReg, $bbanAcct)
    {
        $this->check($bbanReg, $bbanAcct, true);
    }

    /**
     * @covers \NordeaPayment\BBAN::__construct
     */
    public function testInvalidChars()
    {
        $this->check('CZ28', '0300-0080', false);
        $this->check('CZ28', '0300:0080', false);
    }

    /**
     * @covers \NordeaPayment\BBAN::format
     */
    public function testFormat()
    {
        $bban = new BBAN('123', '1234567');
        $this->assertEquals('01230001234567', $bban->format());
    }

    /**
     * @depends testFormat
     * @dataProvider samplesValid
     * @covers \NordeaPayment\BBAN::__toString
     */
    public function testToString($bbanReg, $bbanAcct)
    {
        $instance = new BBAN($bbanReg, $bbanAcct);
        $this->assertEquals($instance->format(), (string) $instance);
    }

    public function samplesValid()
    {
        return array(
            array('1234', '1234567890'),
            array('333', '666666'),
            array('2149', '4371257077'),
        );
    }

    protected function check($bbanReg, $bbanAcct, $valid)
    {
        $exception = false;
        try {
            $temp = new BBAN($bbanReg, $bbanAcct);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
