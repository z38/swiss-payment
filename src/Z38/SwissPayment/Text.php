<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

class Text
{
    const TEXT_NON_CH = '/[^A-Za-z0-9 .,:\'\/()?+\-!"#%&*;<>÷=@_$£[\]{}\` ́~àáâåäçèéêëìíîïñòóôöùúûüýßÀÁÂÅÄÇÈÉÊËÌÍÎÏÒÓÔÖÙÚÛÜÑ]+/u';
    const TEXT_NON_SWIFT = '/[^A-Za-z0-9 .,:\'\/()?+\-]+/';

    /**
     * Sanitizes and trims a string to conform to the Swiss character
     * set.
     *
     * @param string|null $input
     * @param int         $maxLength
     *
     * @return string The sanitized string
     */
    public static function sanitize($input, $maxLength)
    {
        $input = preg_replace('/\s+/', ' ', (string) $input);
        $input = trim(preg_replace(self::TEXT_NON_CH, '', $input));

        return function_exists('mb_substr') ? mb_substr($input, 0, $maxLength, 'UTF-8') : substr($input, 0, $maxLength);
    }

    /**
     * Sanitizes and trims a string to conform to the Swiss character
     * set.
     *
     * @param string|null $input
     * @param int         $maxLength
     *
     * @return string|null The sanitized string or null if it is empty.
     */
    public static function sanitizeOptional($input, $maxLength)
    {
        $sanitized = self::sanitize($input, $maxLength);

        return $sanitized !== '' ? $sanitized : null;
    }

    /**
     * @internal
     */
    public static function assertOptional($input, $maxLength)
    {
        if ($input === null) {
            return null;
        }

        return self::assert($input, $maxLength);
    }

    /**
     * @internal
     */
    public static function assert($input, $maxLength)
    {
        return self::assertNotPattern($input, $maxLength, self::TEXT_NON_CH);
    }

    /**
     * @internal
     */
    public static function assertIdentifier($input)
    {
        $input = self::assertNotPattern($input, 35, self::TEXT_NON_SWIFT);
        if ($input[0] === '/' || strpos($input, '//') !== false) {
            throw new InvalidArgumentException('The identifier contains unallowed slashes.');
        }

        return $input;
    }

    /**
     * @internal
     */
    public static function assertCountryCode($input)
    {
        if (!preg_match('/^[A-Z]{2}$/', $input)) {
            throw new InvalidArgumentException('The country code is invalid.');
        }

        return $input;
    }

    protected static function assertNotPattern($input, $maxLength, $pattern)
    {
        $length = function_exists('mb_strlen') ? mb_strlen($input, 'UTF-8') : strlen($input);
        if (!is_string($input) || $length === 0 || $length > $maxLength) {
            throw new InvalidArgumentException(sprintf('The string can not be empty or longer than %d characters.', $maxLength));
        }
        if (preg_match($pattern, $input)) {
            throw new InvalidArgumentException(sprintf('The string %s contains invalid characters.', $input));
        }

        return $input;
    }

    /**
     * @internal
     */
    public static function xml(DOMDocument $doc, $tag, $content)
    {
        $element = $doc->createElement($tag);
        $element->appendChild($doc->createTextNode($content));

        return $element;
    }
}
