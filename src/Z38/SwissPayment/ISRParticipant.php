<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * ISRParticipant holds an ISR participation number
 */
class ISRParticipant implements AccountInterface
{
    /**
     * @var string
     */
    protected $number;

    /**
     * Constructor
     *
     * @param string $number
     *
     * @throws \InvalidArgumentException When the participation number is not valid.
     */
    public function __construct($number)
    {
        if (preg_match('/^([0-9]{2})-([0-9]{1,6})-([0-9])$/', $number, $dashMatches)) {
            $this->number = sprintf('%s%06s%s', $dashMatches[1], $dashMatches[2], $dashMatches[3]);
        } elseif (preg_match('/^[0-9]{9}$/', $number)) {
            $this->number = $number;
        } else {
            throw new InvalidArgumentException('ISR participant number is not properly formatted.');
        }
    }

    /**
     * Format the participant number
     *
     * @return string The formatted participant number
     */
    public function format()
    {
        return sprintf('%s-%s-%s', substr($this->number, 0, 2), ltrim(substr($this->number, 2, 6), '0'), substr($this->number, 8));
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $root = $doc->createElement('Id');
        $other = $doc->createElement('Othr');
        $other->appendChild($doc->createElement('Id', $this->number));
        $root->appendChild($other);

        return $root;
    }
}
