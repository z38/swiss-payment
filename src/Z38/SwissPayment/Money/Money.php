<?php

namespace Z38\SwissPayment\Money;

/**
 * Base class for all currencies.
 */
abstract class Money implements MoneyInterface
{
    /**
     * @var int
     */
    protected $cents;

    /**
     * Constructor.
     *
     * @param int $cents Amount of money in cents.
     */
    public function __construct($cents)
    {
        $this->cents = intval($cents);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getCurrency();

    /**
     * Gets the number of decimals
     *
     * @return int
     */
    abstract protected function getDecimals();

    /**
     * {@inheritdoc}
     */
    public function format()
    {
        $base = pow(10, $this->getDecimals());
        $sign = ($this->cents < 0 ? '-' : '');

        return sprintf('%s%d.%0'.$this->getDecimals().'d', $sign, intval(abs($this->cents) / $base), abs($this->cents) % $base);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->cents;
    }

    /**
     * {@inheritdoc}
     */
    public function plus(MoneyInterface $addend)
    {
        if ($this->getCurrency() !== $addend->getCurrency()) {
            throw new \InvalidArgumentException('Can not add different currencies');
        }

        return new static($this->cents + $addend->cents);
    }

    /**
     * {@inheritdoc}
     */
    public function minus(MoneyInterface $subtrahend)
    {
        if ($this->getCurrency() !== $subtrahend->getCurrency()) {
            throw new \InvalidArgumentException('Can not subtract different currencies');
        }

        return new static($this->cents - $subtrahend->cents);
    }

    /**
     * {@inheritdoc}
     */
    public function compareTo(MoneyInterface $b)
    {
        if ($this->getCurrency() !== $b->getCurrency()) {
            throw new \InvalidArgumentException('Can not compare different currencies');
        }

        if ($this->getAmount() < $b->getAmount()) {
            return -1;
        } elseif ($this->getAmount() == $b->getAmount()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function equals($obj)
    {
        if (!($obj instanceof self)) {
            return false;
        }

        if ($this->getCurrency() !== $obj->getCurrency()) {
            return false;
        }

        return ($this->getAmount() == $obj->getAmount());
    }
}
