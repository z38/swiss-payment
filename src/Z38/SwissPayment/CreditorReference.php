<?php

namespace Z38\SwissPayment;

/**
 * CreditorReference is a reference based on ISO 11649.
 */
class CreditorReference
{
    const PATTERN = '/^RF[0-9]{2}[0-9A-Z]{1,21}$/';

    /**
     * @var string
     */
    protected $reference;

    /**
     * @param string $reference
     *
     * @throws \InvalidArgumentException When the reference is invalid.
     */
    public function __construct($reference)
    {
        if (!preg_match(self::PATTERN, $reference)) {
            throw new \InvalidArgumentException('Creditor reference is not properly formatted.');
        }

        $this->reference = $reference;
    }

    /**
     * Returns a formatted representation of the reference
     *
     * @return string
     */
    public function format()
    {
        return $this->reference;
    }
}
