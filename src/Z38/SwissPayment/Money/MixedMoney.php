<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in mixed currencies
 */
class MixedMoney extends Money
{
    /**
     * @var int
     */
    protected $decimals;

    /**
     * Constructor
     *
     * @param int $cents    Amount of money in cents
     * @param int $decimals Number of minor units
     */
    public function __construct($cents, $decimals = 0)
    {
        parent::__construct($cents);
        $this->decimals = $decimals;
    }

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
        return $this->decimals;
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
        list($thisCents, $addendCents, $decimals) = self::normalizeDecimals($this, $addend);

        return new static($thisCents + $addendCents, $decimals);
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
        list($thisCents, $subtrahendCents, $decimals) = self::normalizeDecimals($this, $subtrahend);

        return new static($thisCents - $subtrahendCents, $decimals);
    }

    /**
     * Normalizes two amounts such that they have the same number of decimals
     *
     * @param Money $a
     * @param Money $b
     *
     * @return array An array containing the two amounts and number of decimals
     */
    protected static function normalizeDecimals(Money $a, Money $b)
    {
        $decimalsDiff = ($a->getDecimals() - $b->getDecimals());
        $decimalsMax = max($a->getDecimals(), $b->getDecimals());
        if ($decimalsDiff > 0) {
            return [$a->getAmount(), pow(10, $decimalsDiff) * $b->getAmount(), $decimalsMax];
        } elseif ($decimalsDiff < 0) {
            return [pow(10, -$decimalsDiff) * $a->getAmount(), $b->getAmount(), $decimalsMax];
        } else {
            return [$a->getAmount(), $b->getAmount(), $decimalsMax];
        }
    }
}
