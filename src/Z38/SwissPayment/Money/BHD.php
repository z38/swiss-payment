<?php

namespace Z38\SwissPayment\Money;

/**
 * Sum of money in Bahraini dinars
 */
class BHD extends Money
{
    /**
     * {@inheritdoc}
     */
    final public function getCurrency()
    {
        return 'BHD';
    }

    /**
     * {@inheritdoc}
     */
    final protected function getDecimals()
    {
        return 3;
    }
}
