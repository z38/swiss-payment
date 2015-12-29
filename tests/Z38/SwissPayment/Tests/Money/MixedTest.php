<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class MixedTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\Mixed::plus
     */
    public function testAdd()
    {
        $sum = new Money\Mixed(0);
        $sum = $sum->plus(new Money\CHF(4300));
        $sum = $sum->plus(new Money\EUR(1200));

        $this->assertEquals('55.00', $sum->format());
    }

    /**
     * @covers \Z38\SwissPayment\Money\Mixed::minus
     */
    public function testMinus()
    {
        $sum = new Money\Mixed(10000);
        $sum = $sum->minus(new Money\CHF(5000));
        $sum = $sum->minus(new Money\EUR(300));

        $this->assertEquals('47.00', $sum->format());
    }
}
