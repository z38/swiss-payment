<?php

namespace Z38\SwissPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * @internal
 */
class Text
{
    const TEXT_CH = '/^[A-Za-z0-9 .,:\'\/()?+\-!"#%&*;<>÷=@_$£[\]{}\` ́~àáâäçèéêëìíîïñòóôöùúûüýßÀÁÂÄÇÈÉÊËÌÍÎÏÒÓÔÖÙÚÛÜÑ]*$/u';
    const TEXT_SWIFT = '/^[A-Za-z0-9 .,:\'\/()?+\-]*$/';

    public static function assertOptional($input, $maxLength)
    {
        if ($input === null) {
            return null;
        }

        return self::assert($input, $maxLength);
    }

    public static function assert($input, $maxLength)
    {
        return self::assertPattern($input, $maxLength, self::TEXT_CH);
    }

    public static function assertIdentifier($input)
    {
        $input = self::assertPattern($input, 35, self::TEXT_SWIFT);
        if ($input[0] === '/' || strpos($input, '//') !== false) {
            throw new InvalidArgumentException('The identifier contains unallowed slashes.');
        }

        return $input;
    }

    public static function assertCountryCode($input)
    {
        if (!preg_match('/^[A-Z]{2}$/', $input)) {
            throw new InvalidArgumentException('The country code is invalid.');
        }

        return $input;
    }

    protected static function assertPattern($input, $maxLength, $pattern)
    {
        $length = function_exists('mb_strlen') ? mb_strlen($input, 'UTF-8') : strlen($input);
        if (!is_string($input) || $length === 0 || $length > $maxLength) {
            throw new InvalidArgumentException(sprintf('The string can not be empty or longer than %d characters.', $maxLength));
        }
        if (!preg_match($pattern, $input)) {
            throw new InvalidArgumentException('The string contains invalid characters.');
        }

        return $input;
    }

    public static function xml(DOMDocument $doc, $tag, $content)
    {
        $element = $doc->createElement($tag);
        $element->appendChild($doc->createTextNode($content));

        return $element;
    }
}
