<?php

namespace Z38\SwissPayment\Money;

class CHF extends Money
{
    final public function getCurrency()
    {
        return 'CHF';
    }

    final protected function getDecimals()
    {
        return 2;
    }
}
