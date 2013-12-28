<?php

namespace Z38\SwissPayment\Money;

/**
 * Base class for all currencies.
 */
abstract class Money
{
    protected $cents;

    /**
     * Constructor.
     *
     * @param int $cents Amount of money in cents.
     */
    public function __construct($cents)
    {
        $this->cents = intval($cents);
    }

    /**
     * Gets the currency code
     *
     * @return string An ISO 4217 currency code
     */
    abstract protected function getCurrency();

    /**
     * Gets the number of decimals
     *
     * @return int
     */
    abstract protected function getDecimals();

    /**
     * Returns a formatted string (e.g. 15.560)
     *
     * @return string The formatted value
     */
    public function format()
    {
        $base = pow(10, $this->getDecimals());

        return sprintf('%d.%0'.$this->getDecimals().'d', intval($this->cents / $base), $this->cents % $base);
    }

    /**
     * Returns the sum of this and an other amount of money
     *
     * @return Money The sum
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function plus(Money $b)
    {
        if ($this->getCurrency() != $b->getCurrency()) {
            throw new \InvalidArgumentException('Can not add different currencies');
        }

        return new static($this->cents + $b->cents);
    }
}
