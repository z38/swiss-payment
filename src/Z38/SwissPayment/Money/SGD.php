<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Singapore dollars
 */
class SGD extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'SGD';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
