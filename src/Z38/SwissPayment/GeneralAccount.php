<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * GeneralAccount holds details about an account which is not covered by any of the other classes
 */
class GeneralAccount implements AccountInterface
{
    const MAX_LENGTH = 34;

    /**
     * @var string
     */
    protected $id;

    /**
     * Constructor
     *
     * @param string $id
     *
     * @throws InvalidArgumentException When the account identification exceeds the maximum length.
     */
    public function __construct($id)
    {
        $stringId = (string) $id;
        if (strlen($stringId) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The account identifcation is too long.');
        }

        $this->id = $stringId;
    }

    /**
     * {@inheritdoc}
     */
    public function format()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {
        $root = $doc->createElement('Id');
        $other = $doc->createElement('Othr');
        $other->appendChild($doc->createElement('Id', $this->format()));
        $root->appendChild($other);

        return $root;
    }
}
