<?php

namespace Z38\SwissPayment\Money;

class EUR extends Money
{
    final public function getCurrency()
    {
        return 'EUR';
    }

    final protected function getDecimals()
    {
        return 2;
    }
}
