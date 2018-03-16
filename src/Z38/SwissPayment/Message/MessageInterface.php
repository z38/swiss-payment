<?php

namespace Z38\SwissPayment\Message;

/**
 * General interface for ISO-20022 messages
 */
interface MessageInterface
{
    /**
     * Returns a XML representation of the message
     *
     * @param bool $formatOutput Nicely formats output with indentation and extra space.
     *
     * @return string The XML source
     */
    public function asXml($formatOutput = true);
}
