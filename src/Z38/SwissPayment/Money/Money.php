<?php

namespace Z38\SwissPayment\Money;

abstract class Money
{
    protected $cents;

    public function __construct($cents)
    {
        $this->cents = intval($cents);
    }

    abstract protected function getCurrency();

    abstract protected function getDecimals();

    public function format()
    {
        $base = pow(10, $this->getDecimals());

        return sprintf('%d.%0'.$this->getDecimals().'d', intval($this->cents / $base), $this->cents % $base);
    }

    public function plus(Money $b)
    {
        if ($this->getCurrency() != $b->getCurrency()) {
            throw new \InvalidArgumentException('Can not add different currencies');
        }

        return new static($this->cents + $b->cents);
    }
}
