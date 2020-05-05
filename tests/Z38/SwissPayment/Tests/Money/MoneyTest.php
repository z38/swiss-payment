<?php

namespace Z38\SwissPayment\Tests\Money;

use InvalidArgumentException;
use stdClass;
use Z38\SwissPayment\Money;
use Z38\SwissPayment\Tests\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @covers \Z38\SwissPayment\Money\Money::format
     */
    public function testFormatWithDecimals()
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

    /**
     * @covers \Z38\SwissPayment\Money\Money::format
     */
    public function testFormatWithoutDecimals()
    {
        $zero = new Money\JPY(0);
        $this->assertEquals('0', $zero->format());

        $money = new Money\JPY(123);
        $this->assertEquals('123', $money->format());

        $money = new Money\JPY(-1123);
        $this->assertEquals('-1123', $money->format());
    }

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
        $this->assertFalse($instance->equals(new stdClass()));
        $this->assertFalse($instance->equals(new Money\EUR(-451)));
        $this->assertFalse($instance->equals(new Money\CHF(-41)));
    }

    /**
     * @dataProvider validSamplePairs
     * @covers       \Z38\SwissPayment\Money\Money::plus
     * @covers       \Z38\SwissPayment\Money\Money::minus
     * @covers       \Z38\SwissPayment\Money\Money::compareTo
     * @param $a
     * @param $b
     * @param $expectedSum
     * @param $expectedDiff
     * @param $expectedComparison
     */
    public function testBinaryOperands($a, $b, $expectedSum, $expectedDiff, $expectedComparison)
    {
        $this->assertTrue($expectedSum->equals($a->plus($b)));
        $this->assertTrue($expectedDiff->equals($a->minus($b)));
        $this->assertEquals($expectedComparison, $a->compareTo($b));
    }

    /**
     *
     * @dataProvider invalidSamplePairs
     * @covers       \Z38\SwissPayment\Money\Money::plus
     * @param $a
     * @param $b
     */
    public function testInvalidPlus($a, $b)
    {
        $this->expectException(InvalidArgumentException::class);
        $a->plus($b);
    }

    /**
     *
     * @dataProvider invalidSamplePairs
     * @covers       \Z38\SwissPayment\Money\Money::minus
     * @param $a
     * @param $b
     */
    public function testInvalidMinus($a, $b)
    {
        $this->expectException(InvalidArgumentException::class);
        $a->minus($b);
    }

    /**
     *
     * @dataProvider invalidSamplePairs
     * @covers       \Z38\SwissPayment\Money\Money::minus
     * @param $a
     * @param $b
     */
    public function testInvalidCompareTo($a, $b)
    {
        $this->expectException(InvalidArgumentException::class);
        $a->compareTo($b);
    }

    public function validSamplePairs()
    {
        return [
            [new Money\CHF(17400), new Money\CHF(19635), new Money\CHF(37035), new Money\CHF(-2235), -1],
            [new Money\CHF(17400), new Money\CHF(4391), new Money\CHF(21791), new Money\CHF(13009), 1],
            [new Money\CHF(400), new Money\CHF(-400), new Money\CHF(0), new Money\CHF(800), 1],
            [new Money\CHF(400), new Money\CHF(400), new Money\CHF(800), new Money\CHF(0), 0],
        ];
    }

    public function invalidSamplePairs()
    {
        return [
            [new Money\CHF(17400), new Money\EUR(19635)],
        ];
    }
}
