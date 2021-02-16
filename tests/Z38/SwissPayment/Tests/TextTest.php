<?php

namespace Z38\SwissPayment\Tests;

use DOMDocument;
use Z38\SwissPayment\Text;

/**
 * @coversDefaultClass \Z38\SwissPayment\Text
 */
class TextTest extends TestCase
{
    public function testAssertTooLong()
    {
        $this->expectException(\InvalidArgumentException::class);
        Text::assert('abcd', 3);
    }

    public function testAssertMaximumLength()
    {
        self::assertSame('abcd', Text::assert('abcd', 4));
    }

    public function testAssertUnicode()
    {
        self::assertSame('÷ß', Text::assert('÷ß', 2));
    }

    public function testAssertInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        Text::assert('°', 10);
    }

    public function testAssertIdentiferBeginsWithSlash()
    {
        $this->expectException(\InvalidArgumentException::class);
        Text::assertIdentifier('/abc');
    }

    public function testAssertIdentiferContainsDoubleSlash()
    {
        $this->expectException(\InvalidArgumentException::class);
        Text::assertIdentifier('ab//c');
    }

    public function testAssertIdentiferContainsSlash()
    {
        self::assertSame('ab/c', Text::assertIdentifier('ab/c'));
    }

    public function testAssertCountryCodeUppercase()
    {
        $this->expectException(\InvalidArgumentException::class);
        Text::assertCountryCode('ch');
    }

    /**
     * @dataProvider sanitizeSamples
     */
    public function testSanitize($input, $expected)
    {
        self::assertSame($expected, Text::sanitize($input, 3));
    }

    public function sanitizeSamples()
    {
        return [
            ["\t  \t", ''],
            ['°¬◆😀', ''],
            ['  中文A B中文C  ', 'A B'],
            ["ä \nÇ \n \nz", 'ä Ç'],
            ['äääää', 'äää'],
        ];
    }

    public function testSanitizeOptional()
    {
        self::assertSame(null, Text::sanitizeOptional(" \t ° ° \t", 100));
    }

    public function testXml()
    {
        $doc = new DOMDocument();

        $element = Text::xml($doc, 'abc', '<>&');

        self::assertSame('<abc>&lt;&gt;&amp;</abc>', $doc->saveXML($element));
    }
}
