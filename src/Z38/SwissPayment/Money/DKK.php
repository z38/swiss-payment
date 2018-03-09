<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Danish kroner
 */
class DKK extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'DKK';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 2;
    }
}
