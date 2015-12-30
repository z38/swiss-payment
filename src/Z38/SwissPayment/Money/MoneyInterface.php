<?php

namespace Z38\SwissPayment\Money;


interface MoneyInterface
{
    /**
     * Gets the currency code
     *
     * @return string|null An ISO 4217 currency code or null if currency is not known
     */
    public function getCurrency();

    /**
     * Returns a formatted string (e.g. 15.560)
     *
     * @return string The formatted value
     */
    public function format();

    /**
     * Returns the amount of money in cents
     *
     * @return int The amount in cents
     */
    public function getAmount();

    /**
     * Returns the sum of this and an other amount of money
     *
     * @param MoneyInterface $addend The addend
     *
     * @return MoneyInterface The sum
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function plus(MoneyInterface $addend);

    /**
     * Returns the subtraction of this and an other amount of money
     *
     * @param MoneyInterface $subtrahend The subtrahend
     *
     * @return MoneyInterface The difference
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function minus(MoneyInterface $subtrahend);

    /**
     * Compares this instance with an other instance.
     *
     * @param MoneyInterface $b The instance to which this instance is to be compared.
     *
     * @return int -1, 0 or 1 as this instance is less than, equal to, or greater than $b
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function compareTo(MoneyInterface $b);

    /**
     * Returns true if the argument contains the same amount and the same currency.
     *
     * @param object $obj
     *
     * @return bool True if $obj is equal to this instance
     */
    public function equals($obj);
}
