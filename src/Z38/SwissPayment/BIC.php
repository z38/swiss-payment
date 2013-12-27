<?php

namespace Z38\SwissPayment;

class BIC
{
    const PATTERN = '/^[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}$/';

    protected $bic;

    public function __construct($bic)
    {
        if (!preg_match(self::PATTERN, $bic)) {
            throw new \InvalidArgumentException('BIC is not properly formatted.');
        }

        $this->bic = $bic;
    }

    public function format()
    {
        return $this->bic;
    }
}
