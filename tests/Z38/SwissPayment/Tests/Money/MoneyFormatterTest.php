<?php

namespace Z38\SwissPayment\Tests\Money;

use Money\Money;
use Z38\SwissPayment\Tests\TestCase;
use Z38\SwissPayment\Money\MoneyFormatter;

class MoneyFormatterTest extends TestCase
{
    public function testDecimals()
    {
        $formatter = new MoneyFormatter(2);

        $this->assertSame('0.00', $formatter->format(Money::CHF(0)));

        $this->assertSame('0.01', $formatter->format(Money::CHF(1)));
        $this->assertSame('0.99', $formatter->format(Money::CHF(99)));
        $this->assertSame('1.00', $formatter->format(Money::CHF(100)));
        $this->assertSame('2.34', $formatter->format(Money::CHF(234)));
        $this->assertSame('9.99', $formatter->format(Money::CHF(999)));
        $this->assertSame('99.99', $formatter->format(Money::CHF(9999)));

        $this->assertSame('-0.01', $formatter->format(Money::CHF(-1)));
        $this->assertSame('-0.99', $formatter->format(Money::CHF(-99)));
        $this->assertSame('-1.00', $formatter->format(Money::CHF(-100)));
        $this->assertSame('-2.34', $formatter->format(Money::CHF(-234)));
        $this->assertSame('-9.99', $formatter->format(Money::CHF(-999)));
        $this->assertSame('-99.99', $formatter->format(Money::CHF(-9999)));
    }

    public function testNoDecimals()
    {
        $formatter = new MoneyFormatter(0);

        $this->assertSame('0', $formatter->format(Money::CHF(0)));
        $this->assertSame('1', $formatter->format(Money::CHF(1)));
        $this->assertSame('-1', $formatter->format(Money::CHF(-1)));
        $this->assertSame('234', $formatter->format(Money::CHF(234)));
        $this->assertSame('-999', $formatter->format(Money::CHF(-999)));
    }
}
