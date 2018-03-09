<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Moroccan dirhams
 */
class MAD extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'MAD';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
