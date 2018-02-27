<?php

namespace Z38\SwissPayment\Tests;

use Z38\SwissPayment\ISRParticipant;

/**
 * @coversDefaultClass \Z38\SwissPayment\ISRParticipant
 */
class ISRParticipantTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     */
    public function testValid($number)
    {
        $this->assertInstanceOf('Z38\SwissPayment\ISRParticipant', new ISRParticipant($number));
    }

    /**
     * @dataProvider invalidSamples
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalid($number)
    {
        new ISRParticipant($number);
    }

    /**
     * @covers ::format
     */
    public function testFormat()
    {
        $instance = new ISRParticipant('010001628');
        $this->assertEquals('01-162-8', $instance->format());
    }

    public function validSamples()
    {
        return [
            ['01-394971-8'],
            ['010059136'],
            ['01-137-5'],
        ];
    }

    public function invalidSamples()
    {
        return [
            ['01-394971-9'],
            ['01-7777777-2'],
            ['80-470-3-1'],
            ['12345678'],
            ['1234567890'],
        ];
    }
}
