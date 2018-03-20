<?php

namespace Z38\SwissPayment\Message;

/**
 * AbstractMessages eases message creation using DOM
 */
abstract class AbstractMessage implements MessageInterface
{
    const SCHEMA_LOCATION = 'http://www.six-interbank-clearing.com/de/%s';

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
     * Builds a DOM document of the message
     *
     * @return \DOMDocument
     */
    public function asDom()
    {
        $schema = $this->getSchemaName();
        $ns = sprintf(self::SCHEMA_LOCATION, $schema);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('Document');
        $root->setAttribute('xmlns', $ns);
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', sprintf('%s %s', $ns, $schema));
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
        return '0.5.0';
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

        $root->appendChild($doc->createElement('Nm', $this->getSoftwareName()));
        $root->appendChild($doc->createElement('Othr', $this->getSoftwareVersion()));

        return $root;
    }
}
