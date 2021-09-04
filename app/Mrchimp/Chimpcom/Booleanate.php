<?php

namespace Mrchimp\Chimpcom;

/**
 * Check if a string is affirmative or negative.
 */
class Booleanate
{
    /**
     * These words count as affirmative
     * @var array
     */
    private static $yes_words = array(
        'affirmative',
        'ja',
        'ok',
        'oui',
        'roger',
        'sure',
        'y',
        'ya',
        'yea',
        'yeah',
        'yes',
        'yup',
        'yep',
        'yar',
        'yarr',
        'yarrr',
        'yarp',
        'yeppers',
        'do it',
        'fuck yes',
        'hell yes',
        '1',
    );

    /**
     * These words count as negative
     * @var array
     */
    private static $no_words = array(
        'abort',
        'cancel',
        'n',
        'negative',
        'no',
        'non',
        'nope',
        'nein',
        'fuck no',
        'hell no',
        '0',
    );

    /**
     * Returns true if parameter is yes-like
     */
    public static function isAffirmative(string $val): bool
    {
        return in_array(strtolower($val), self::$yes_words);
    }

    /**
     * Returns true if parameter is no-like
     */
    public static function isNegative(string $val): bool
    {
        return in_array(strtolower($val), self::$no_words);
    }
}
