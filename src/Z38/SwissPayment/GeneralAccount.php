<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * GeneralAccount holds details about an account which is not covered by any of the other classes
 */
class GeneralAccount implements AccountInterface
{
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
        $this->id = Text::assert($id, 34);
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
        $other->appendChild(Text::xml($doc, 'Id', $this->format()));
        $root->appendChild($other);

        return $root;
    }
}
