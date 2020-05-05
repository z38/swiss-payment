<?php

namespace Z38\SwissPayment\Tests\PaymentInformation;

use DOMDocument;
use InvalidArgumentException;
use Z38\SwissPayment\PaymentInformation\CategoryPurposeCode;
use Z38\SwissPayment\Tests\TestCase;

/**
 * @coversDefaultClass \Z38\SwissPayment\PaymentInformation\CategoryPurposeCode
 */
class CategoryPurposeCodeTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     * @param $code
     */
    public function testValid($code)
    {
        $this->assertInstanceOf('Z38\SwissPayment\PaymentInformation\CategoryPurposeCode', new CategoryPurposeCode($code));
    }

    public function validSamples()
    {
        return [
            ['SALA'], // salary payment
            ['PENS'], // pension payment
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
        new CategoryPurposeCode($code);
    }

    public function invalidSamples()
    {
        return [
            [''],
            ['sala'],
            ['SAL'],
            [' SALA'],
            ['B112'],
        ];
    }

    /**
     * @covers ::asDom
     */
    public function testAsDom()
    {
        $doc = new DOMDocument();
        $iid = new CategoryPurposeCode('SALA');

        $xml = $iid->asDom($doc);

        $this->assertSame('Cd', $xml->nodeName);
        $this->assertSame('SALA', $xml->textContent);
    }
}
