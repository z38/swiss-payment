<?php

namespace Z38\SwissPayment\Tests;

use InvalidArgumentException;
use Z38\SwissPayment\PostalAccount;

/**
 * @coversDefaultClass \Z38\SwissPayment\PostalAccount
 */
class PostalAccountTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     */
    public function testValid($postalAccount)
    {
        $this->assertInstanceOf('Z38\SwissPayment\PostalAccount', new PostalAccount($postalAccount));
    }

    public function validSamples()
    {
        return [
            ['01-1613-8'],
            ['01-200099-8'],
            ['30-38112-0'],
            ['61-662139-8'],
            ['80-2-2'],
            ['80-470-3'],
            ['87-344666-2'],
        ];
    }

    /**
     * @dataProvider invalidFormatSamples
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Postal account number is not properly formatted.
     */
    public function testInvalidFormat($postalAccount)
    {
        new PostalAccount($postalAccount);
    }

    public function invalidFormatSamples()
    {
        return [
            ['1-1613-8'],
            ['4032138'],
            ['40.3213.8'],
            ['40-003213-8'],
            ['40-3213-28'],
        ];
    }

    /**
     * @dataProvider invalidCheckDigitSamples
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Postal account number has an invalid check digit.
     */
    public function testInvalidCheckDigit($postalAccount)
    {
        new PostalAccount($postalAccount);
    }

    public function invalidCheckDigitSamples()
    {
        return [
            ['01-1613-1'],
            ['30-38112-1'],
            ['80-2-1'],
        ];
    }

    /**
     * @dataProvider validSamples
     * @covers ::format
     */
    public function testFormat($postalAccount)
    {
        $instance = new PostalAccount($postalAccount);
        $this->assertEquals($postalAccount, $instance->format());
    }
}
