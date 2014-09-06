<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class CHFTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\Money::format
     */
    public function testFormat()
    {
        $zero = new Money\CHF(0);
        $this->assertEquals('0.00', $zero->format());

        $money = new Money\CHF(1234567);
        $this->assertEquals('12345.67', $money->format());

        $money = new Money\CHF(-1234567);
        $this->assertEquals('-12345.67', $money->format());

        $money = new Money\CHF(-2);
        $this->assertEquals('-0.02', $money->format());
    }
}
