<?php

namespace Z38\SwissPayment\Tests\Money;

use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\Money::getAmount
     */
    public function testGetAmount()
    {
        $instance = new Money\CHF(345);
        $this->assertEquals(345, $instance->getAmount());

        $instance = new Money\CHF(-345);
        $this->assertEquals(-345, $instance->getAmount());

        $instance = new Money\CHF(0);
        $this->assertEquals(0, $instance->getAmount());
    }

    /**
     * @covers \Z38\SwissPayment\Money\Money::equals
     */
    public function testEquals()
    {
        $instance = new Money\CHF(-451);

        $this->assertTrue($instance->equals($instance));
        $this->assertTrue($instance->equals(new Money\CHF(-451)));

        $this->assertFalse($instance->equals(false));
        $this->assertFalse($instance->equals(null));
        $this->assertFalse($instance->equals(new \stdClass()));
        $this->assertFalse($instance->equals(new Money\EUR(-451)));
        $this->assertFalse($instance->equals(new Money\CHF(-41)));
    }

    /**
     * @dataProvider validSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::plus
     * @covers \Z38\SwissPayment\Money\Money::minus
     * @covers \Z38\SwissPayment\Money\Money::compareTo
     */
    public function testBinaryOperands($a, $b, $expectedSum, $expectedDiff, $expectedComparison)
    {
        $this->assertTrue($expectedSum->equals($a->plus($b)));
        $this->assertTrue($expectedDiff->equals($a->minus($b)));
        $this->assertEquals($expectedComparison, $a->compareTo($b));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::plus
     */
    public function testInvalidPlus($a, $b)
    {
        $a->plus($b);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::minus
     */
    public function testInvalidMinus($a, $b)
    {
        $a->minus($b);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::minus
     */
    public function testInvalidCompareTo($a, $b)
    {
        $a->compareTo($b);
    }

    public function validSamplePairs()
    {
        return array(
            array(new Money\CHF(17400), new Money\CHF(19635), new Money\CHF(37035), new Money\CHF(-2235), -1),
            array(new Money\CHF(17400), new Money\CHF(4391), new Money\CHF(21791), new Money\CHF(13009), 1),
            array(new Money\CHF(400), new Money\CHF(-400), new Money\CHF(0), new Money\CHF(800), 1),
            array(new Money\CHF(400), new Money\CHF(400), new Money\CHF(800), new Money\CHF(0), 0),
        );
    }

    public function invalidSamplePairs()
    {
        return array(
            array(new Money\CHF(17400), new Money\EUR(19635)),
        );
    }
}
