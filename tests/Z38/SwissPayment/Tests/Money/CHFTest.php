<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class CHFTest extends TestCase
{
    public function testFormat()
    {
        $zero = new Money\CHF(0);
        $this->assertEquals('0.00', $zero->format());

        $money = new Money\CHF(1234567);
        $this->assertEquals('12345.67', $money->format());
    }

    public function testPlus()
    {
        $a = new Money\CHF(98761);
        $b = new Money\CHF(1234);

        $this->assertEquals('999.95', $a->plus($b)->format());
        $this->assertEquals('987.61', $a->format());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPlus()
    {
        $a = new Money\CHF(17400);
        $b = new Money\EUR(19635);

        $a->plus($b);
    }
}
