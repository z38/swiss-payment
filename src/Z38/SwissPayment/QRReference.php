<?php

namespace Z38\SwissPayment;

/**
 * QRReference
 */
class QRReference
{
    const PATTERN = '/^[0-9]{27}$/';

    /**
     * @var string
     */
    protected $reference;

    /**
     * Constructor
     *
     * @param string $reference
     *
     * @throws \InvalidArgumentException When the reference is invalid.
     */
    public function __construct($reference)
    {
        if (!preg_match(self::PATTERN, $reference)) {
            throw new \InvalidArgumentException('QR reference is not properly formatted.');
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
