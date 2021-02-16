<?php

namespace Z38\SwissPayment\Tests\Money;

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
        self::assertEquals('0.00', $zero->format());

        $money = new Money\CHF(1234567);
        self::assertEquals('12345.67', $money->format());

        $money = new Money\CHF(-1234567);
        self::assertEquals('-12345.67', $money->format());

        $money = new Money\CHF(-2);
        self::assertEquals('-0.02', $money->format());
    }

    /**
     * @covers \Z38\SwissPayment\Money\Money::format
     */
    public function testFormatWithoutDecimals()
    {
        $zero = new Money\JPY(0);
        self::assertEquals('0', $zero->format());

        $money = new Money\JPY(123);
        self::assertEquals('123', $money->format());

        $money = new Money\JPY(-1123);
        self::assertEquals('-1123', $money->format());
    }

    /**
     * @covers \Z38\SwissPayment\Money\Money::getAmount
     */
    public function testGetAmount()
    {
        $instance = new Money\CHF(345);
        self::assertEquals(345, $instance->getAmount());

        $instance = new Money\CHF(-345);
        self::assertEquals(-345, $instance->getAmount());

        $instance = new Money\CHF(0);
        self::assertEquals(0, $instance->getAmount());
    }

    /**
     * @covers \Z38\SwissPayment\Money\Money::equals
     */
    public function testEquals()
    {
        $instance = new Money\CHF(-451);

        self::assertTrue($instance->equals($instance));
        self::assertTrue($instance->equals(new Money\CHF(-451)));

        self::assertFalse($instance->equals(false));
        self::assertFalse($instance->equals(null));
        self::assertFalse($instance->equals(new \stdClass()));
        self::assertFalse($instance->equals(new Money\EUR(-451)));
        self::assertFalse($instance->equals(new Money\CHF(-41)));
    }

    /**
     * @dataProvider validSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::plus
     * @covers \Z38\SwissPayment\Money\Money::minus
     * @covers \Z38\SwissPayment\Money\Money::compareTo
     */
    public function testBinaryOperands($a, $b, $expectedSum, $expectedDiff, $expectedComparison)
    {
        self::assertTrue($expectedSum->equals($a->plus($b)));
        self::assertTrue($expectedDiff->equals($a->minus($b)));
        self::assertEquals($expectedComparison, $a->compareTo($b));
    }

    /**
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::plus
     */
    public function testInvalidPlus($a, $b)
    {
        $this->expectException(\InvalidArgumentException::class);
        $a->plus($b);
    }

    /**
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::minus
     */
    public function testInvalidMinus($a, $b)
    {
        $this->expectException(\InvalidArgumentException::class);
        $a->minus($b);
    }

    /**
     * @dataProvider invalidSamplePairs
     * @covers \Z38\SwissPayment\Money\Money::minus
     */
    public function testInvalidCompareTo($a, $b)
    {
        $this->expectException(\InvalidArgumentException::class);
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
