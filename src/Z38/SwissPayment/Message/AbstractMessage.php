<?php

namespace Z38\SwissPayment\Message;

abstract class AbstractMessage implements MessageInterface
{
    const SCHEMA_LOCATION = 'http://www.six-interbank-clearing.com/de/%s';

    abstract protected function buildDom(\DOMDocument $doc);

    abstract protected function getSchemaName();

    public function asDom()
    {
        $schema = $this->getSchemaName();
        $ns = sprintf(self::SCHEMA_LOCATION, $schema);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Document');
        $root->setAttribute('xmlns', $ns);
        $root->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute('xsi:schemaLocation', sprintf("%s %s", $ns, $schema));
        $root->appendChild($this->buildDom($doc));
        $doc->appendChild($root);

        return $doc;
    }

    public function asXml()
    {
        return $this->asDom()->saveXML();
    }

    public function getSoftwareName()
    {
        return 'Z38_SwissPayment';
    }

    public function getSoftwareVersion()
    {
        return 'dev-master';
    }

    protected function buildContactDetails(\DOMDocument $doc)
    {
        $root = $doc->createElement('CtctDtls');

        $root->appendChild($doc->createElement('Nm', $this->getSoftwareName()));
        $root->appendChild($doc->createElement('Othr', $this->getSoftwareVersion()));

        return $root;
    }
}
