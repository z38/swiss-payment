<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class GBPTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\Money::format
     */
    public function testFormat()
    {
        $zero = new Money\GBP(0);
        $this->assertEquals('0.00', $zero->format());

        $money = new Money\GBP(1234567);
        $this->assertEquals('12345.67', $money->format());

        $money = new Money\GBP(-1234567);
        $this->assertEquals('-12345.67', $money->format());

        $money = new Money\GBP(-2);
        $this->assertEquals('-0.02', $money->format());

        $money = new Money\GBP(-2);
        $addedMoney = $money->plus(new Money\GBP(4));
        $this->assertEquals('0.02', $addedMoney->format());

        $money = new Money\GBP(-2);
        $addedMoney = $money->plus(new Money\GBP(4));
        $subMoney = $addedMoney->minus(new Money\GBP(10));
        $this->assertEquals('-0.08', $subMoney->format());
    }
}
