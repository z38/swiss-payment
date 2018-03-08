<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Swiss francs
 */
class MXN extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'MXN';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
