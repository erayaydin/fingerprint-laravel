<?php

namespace ErayAydin\Fingerprint\Console;

/**
 * Class ConsoleHelper
 *
 * Provides helper methods for console output with colored text.
 */
class ConsoleHelper
{
    /**
     * Returns the text formatted in green color to indicate success.
     *
     * @param  string  $text  The text to format.
     * @return string The formatted text.
     */
    public static function success(string $text): string
    {
        return self::colorText('green', $text);
    }

    /**
     * Returns the text formatted in red color to indicate an error.
     *
     * @param  string  $text  The text to format.
     * @return string The formatted text.
     */
    public static function error(string $text): string
    {
        return self::colorText('red', $text);
    }

    /**
     * Returns the text formatted in yellow color to indicate a warning.
     *
     * @param  string  $text  The text to format.
     * @return string The formatted text.
     */
    public static function warning(string $text): string
    {
        return self::colorText('yellow', $text);
    }

    /**
     * Returns the text formatted in blue color to provide information.
     *
     * @param  string  $text  The text to format.
     * @return string The formatted text.
     */
    public static function info(string $text): string
    {
        return self::colorText('blue', $text);
    }

    /**
     * Returns the text formatted with the specified color and options.
     *
     * @param  string  $color  The color to use for the text.
     * @param  string  $text  The text to format.
     * @param  string  $options  The options to apply to the text formatting.
     * @return string The formatted text.
     */
    public static function colorText(string $color, string $text, string $options = 'bold'): string
    {
        return "<fg=$color;options=$options>$text</>";
    }
}
