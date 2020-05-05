<?php

namespace Z38\SwissPayment\Tests\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\TransactionInformation\PurposeCode;

/**
 * @coversDefaultClass \Z38\SwissPayment\TransactionInformation\PurposeCode
 */
class PurposeCodeTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     * @param $code
     */
    public function testValid($code)
    {
        $this->assertInstanceOf('Z38\SwissPayment\TransactionInformation\PurposeCode', new PurposeCode($code));
    }

    public function validSamples()
    {
        return [
            ['SALA'], // salary payment
            ['PENS'], // pension payment
            ['DNTS'], // dental services
            ['B112'], // US mutual fund trailer fee (12b-1) payment
        ];
    }

    /**
     * @dataProvider invalidSamples
     * @covers ::__construct
     *
     * @param $code
     */
    public function testInvalid($code)
    {
        $this->expectException(InvalidArgumentException::class);
        new PurposeCode($code);
    }

    public function invalidSamples()
    {
        return [
            [''],
            ['sala'],
            ['SAL'],
            [' SALA'],
        ];
    }

    /**
     * @covers ::asDom
     */
    public function testAsDom()
    {
        $doc = new DOMDocument();
        $iid = new PurposeCode('PHON');

        $xml = $iid->asDom($doc);

        $this->assertSame('Cd', $xml->nodeName);
        $this->assertSame('PHON', $xml->textContent);
    }
}
