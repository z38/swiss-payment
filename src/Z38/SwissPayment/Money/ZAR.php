<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in South African rand
 */
class ZAR extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'ZAR';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
