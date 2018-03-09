<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Hong Kong dollars
 */
class HKD extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'HKD';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
