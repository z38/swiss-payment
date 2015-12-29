<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Mixed currencies
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

    /**
     * Compares this instance with an other instance.
     *
     * @param Money $b The instance to which this instance is to be compared.
     *
     * @return int -1, 0 or 1 as this instance is less than, equal to, or greater than $b
     */
    public function compareTo(Money $b)
    {
        if ($this->getAmount() < $b->getAmount()) {
            return -1;
        } elseif ($this->getAmount() == $b->getAmount()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Returns true if the argument contains the same amount and the same currency.
     *
     * @param object $obj
     *
     * @return bool True if $obj is equal to this instance
     */
    public function equals($obj)
    {
        if (!($obj instanceof Money)) {
            return false;
        }

        return ($this->getAmount() == $obj->getAmount());
    }
}
