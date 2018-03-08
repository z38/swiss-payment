<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Swiss francs
 */
class AED extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'AED';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
