<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\BC;
use Z38\SwissPayment\IBAN;

/**
 * @coversDefaultClass \Z38\SwissPayment\BC
 */
class BCTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testValid()
    {
        $this->check('9222', true);
        $this->check('00432', true);
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidLength()
    {
        $this->check('00000000', false);
        $this->check('10000000', false);
        $this->check('11', false);
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidChars()
    {
        $this->check('FFF', false);
        $this->check('0 11', false);
    }

    /**
     * @covers ::format
     */
    public function testFormat()
    {
        $instance = new BC('00355');
        $this->assertSame('355', $instance->format());
    }

    /**
     * @covers ::fromIBAN
     */
    public function testFromIBAN()
    {
        $instance = BC::fromIBAN(new IBAN('CH31 8123 9000 0012 4568 9'));
        $this->assertSame('81239', $instance->format());
    }

    /**
     * @cover ::fromIban
     * @expectedException \InvalidArgumentException
     */
    public function testFromIBANForeign()
    {
        BC::fromIBAN(new IBAN('GB29 NWBK 6016 1331 9268 19'));
    }

    protected function check($bc, $valid)
    {
        $exception = false;
        try {
            $temp = new BC($bc);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
