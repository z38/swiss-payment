<?php

namespace Z38\SwissPayment\Message;

use Z38\SwissPayment\Text;

/**
 * AbstractMessages eases message creation using DOM
 */
abstract class AbstractMessage implements MessageInterface
{
    /**
     * Builds the DOM of the actual message
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    abstract protected function buildDom(\DOMDocument $doc);

    /**
     * Gets the name of the schema
     *
     * @return string
     */
    abstract protected function getSchemaName();

    /**
     * Gets the location of the schema
     *
     * @return string|null The location or null
     */
    abstract protected function getSchemaLocation();

    /**
     * Builds a DOM document of the message
     *
     * @return \DOMDocument
     */
    public function asDom()
    {
        $schema = $this->getSchemaName();
        $location = $this->getSchemaLocation();

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('Document');
        $root->setAttribute('xmlns', $schema);
        if ($location !== null) {
            $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $root->setAttribute('xsi:schemaLocation', $schema.' '.$location);
        }
        $root->appendChild($this->buildDom($doc));
        $doc->appendChild($root);

        return $doc;
    }

    /**
     * {@inheritdoc}
     */
    public function asXml()
    {
        return $this->asDom()->saveXML();
    }

    /**
     * Returns the name of the software used to create the message
     *
     * @return string
     */
    public function getSoftwareName()
    {
        return 'Z38_SwissPayment';
    }

    /**
     * Returns the version of the software used to create the message
     *
     * @return string
     */
    public function getSoftwareVersion()
    {
        return '0.7.0';
    }

    /**
     * Creates a DOM element which contains details about the software used to create the message
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    protected function buildContactDetails(\DOMDocument $doc)
    {
        $root = $doc->createElement('CtctDtls');

        $root->appendChild(Text::xml($doc, 'Nm', $this->getSoftwareName()));
        $root->appendChild(Text::xml($doc, 'Othr', $this->getSoftwareVersion()));

        return $root;
    }
}
