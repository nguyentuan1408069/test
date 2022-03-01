<?php

/**
 * Get an item from an array using "dot" notation.
 *
 * @param  array $array
 * @param  string $key
 * @param  mixed $default
 * @return mixed
 */
if (! function_exists('array_get')) {
    function array_get (array $array, string $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

/**
 * Check whether or not the $needle string is at the beginning of $haystack string
 *
 * @return bool
 */
if (! function_exists('str_start_with')) {
    function str_start_with (string $haystack, string $needle): bool
    {
        $len = strlen($needle);

        return (substr($haystack, 0, $len) === $needle);
    }
}
