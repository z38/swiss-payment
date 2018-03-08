<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Swiss francs
 */
class HUF extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'HUF';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
