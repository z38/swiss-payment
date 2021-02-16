<?php

namespace Z38\SwissPayment\Tests;

use DOMDocument;
use DOMXPath;
use Z38\SwissPayment\IBAN;
use Z38\SwissPayment\IID;

/**
 * @coversDefaultClass \Z38\SwissPayment\IID
 */
class IIDTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     */
    public function testValid($iid)
    {
        self::assertInstanceOf('Z38\SwissPayment\IID', new IID($iid));
    }

    public function validSamples()
    {
        return [
            ['9222'],
            ['00432'],
        ];
    }

    /**
     * @dataProvider invalidSamples
     * @covers ::__construct
     */
    public function testInvalidLength($iid)
    {
        $this->expectException(\InvalidArgumentException::class);
        new IID($iid);
    }

    public function invalidSamples()
    {
        return [
            ['00000000'],
            ['10000000'],
            ['11'],
            ['FFF'],
            ['0 11'],
        ];
    }

    /**
     * @covers ::format
     */
    public function testFormat()
    {
        $instance = new IID('350');
        self::assertSame('00350', $instance->format());
    }

    /**
     * @dataProvider fromIBANSamples
     * @covers ::fromIBAN
     */
    public function testFromIBAN($iban, $iid)
    {
        $instance = IID::fromIBAN(new IBAN($iban));
        self::assertSame($iid, $instance->format());
    }

    public function fromIBANSamples()
    {
        return [
            ['CH31 8123 9000 0012 4568 9', '81239'],
            ['LI21 0881 0000 2324 013A A', '08810'],
        ];
    }

    /**
     * @cover ::fromIban
     */
    public function testFromIBANForeign()
    {
        $this->expectException(\InvalidArgumentException::class);
        IID::fromIBAN(new IBAN('GB29 NWBK 6016 1331 9268 19'));
    }

    /**
     * @cover ::asDom
     */
    public function testAsDom()
    {
        $doc = new DOMDocument();
        $iid = new IID('09000');

        $xml = $iid->asDom($doc);

        $xpath = new DOMXPath($doc);
        self::assertSame('9000', $xpath->evaluate('string(.//MmbId)', $xml));
    }
}
