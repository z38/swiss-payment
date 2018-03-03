<?php

namespace Z38\SwissPayment\Tests;

use DOMDocument;
use Z38\SwissPayment\Text;

/**
 * @coversDefaultClass \Z38\SwissPayment\Text
 */
class TextTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAssertTooLong()
    {
        Text::assert('abcd', 3);
    }

    public function testAssertMaximumLength()
    {
        $this->assertSame('abcd', Text::assert('abcd', 4));
    }

    public function testAssertUnicode()
    {
        $this->assertSame('÷ß', Text::assert('÷ß', 2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAssertInvalid()
    {
        Text::assert('°', 10);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAssertIdentiferBeginsWithSlash()
    {
        Text::assertIdentifier('/abc');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAssertIdentiferContainsDoubleSlash()
    {
        Text::assertIdentifier('ab//c');
    }

    public function testAssertIdentiferContainsSlash()
    {
        $this->assertSame('ab/c', Text::assertIdentifier('ab/c'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAssertCountryCodeUppercase()
    {
        Text::assertCountryCode('ch');
    }

    public function testXml()
    {
        $doc = new DOMDocument();

        $element = Text::xml($doc, 'abc', '<>&');

        $this->assertSame('<abc>&lt;&gt;&amp;</abc>', $doc->saveXML($element));
    }
}
