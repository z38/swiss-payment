<?php

namespace Z38\SwissPayment\Money;

use Money\Money;

/**
 * Formats Money\Money instances
 */
class MoneyFormatter
{
    protected $decimals;

    /**
     * Constructor.
     *
     * @param int $decimals Number of decimals
     */
    public function __construct($decimals = 2)
    {
        $this->decimals = $decimals;
    }

    /**
     * Returns a formatted string (e.g. 15.560)
     *
     * @param Money $money The amount to be formatted
     *
     * @return string The formatted value
     */
    public function format(Money $money)
    {
        $asFloat = floatval($money->getAmount()) / pow(10, $this->decimals);

        return number_format($asFloat, $this->decimals, '.', '');
    }
}
