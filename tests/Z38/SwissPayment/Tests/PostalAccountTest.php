<?php

namespace Z38\SwissPayment\Tests;

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
        self::assertInstanceOf('Z38\SwissPayment\PostalAccount', new PostalAccount($postalAccount));
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
     */
    public function testInvalidFormat($postalAccount)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Postal account number is not properly formatted.');
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
     */
    public function testInvalidCheckDigit($postalAccount)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Postal account number has an invalid check digit.');
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
        self::assertEquals($postalAccount, $instance->format());
    }
}
