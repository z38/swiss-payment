<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class MixedTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\MixedMoney::plus
     */
    public function testPlus()
    {
        $sum = new Money\MixedMoney(0);
        $sum = $sum->plus(new Money\CHF(2456));
        $sum = $sum->plus(new Money\CHF(1000));
        $sum = $sum->plus(new Money\JPY(1200));

        self::assertEquals('1234.56', $sum->format());
    }

    /**
     * @covers \Z38\SwissPayment\Money\MixedMoney::minus
     */
    public function testMinus()
    {
        $sum = new Money\MixedMoney(100);
        $sum = $sum->minus(new Money\CHF(5000));
        $sum = $sum->minus(new Money\CHF(99));
        $sum = $sum->minus(new Money\JPY(300));

        self::assertEquals('-250.99', $sum->format());
    }
}
