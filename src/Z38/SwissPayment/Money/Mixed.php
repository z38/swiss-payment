<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in mixed currencies
 */
class Mixed extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }

    /**
     * Returns the sum of this and an other amount of money
     *
     * @param Money $addend The addend
     *
     * @return Money The sum
     */
    public function plus(Money $addend)
    {
        return new static($this->cents + $addend->cents);
    }

    /**
     * Returns the subtraction of this and an other amount of money
     *
     * @param Money $subtrahend The subtrahend
     *
     * @return Money The difference
     */
    public function minus(Money $subtrahend)
    {
        return new static($this->cents - $subtrahend->cents);
    }
}
