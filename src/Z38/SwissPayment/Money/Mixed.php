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
     * @param MoneyInterface $addend The addend
     *
     * @return MoneyInterface The sum
     */
    public function plus(MoneyInterface $addend)
    {
        return new static($this->cents + $addend->cents);
    }

    /**
     * Returns the subtraction of this and an other amount of money
     *
     * @param MoneyInterface $subtrahend The subtrahend
     *
     * @return MoneyInterface The difference
     */
    public function minus(MoneyInterface $subtrahend)
    {
        return new static($this->cents - $subtrahend->cents);
    }
}
