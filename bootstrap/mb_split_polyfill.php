<?php declare(strict_types=1);

if (!function_exists('mb_split')) {
    /**
     * Basic polyfill for mb_split using preg_split.
     * Note: This does not fully replicate multibyte behavior of mbstring,
     * but prevents fatal errors when mbstring is missing.
     *
     * @param string $pattern PCRE fragment without delimiters (e.g., "\\s+")
     * @param string $string
     * @param int $limit
     *
     * @return array
     */
    function mb_split(string $pattern, string $string, int $limit = -1): array
    {
        $delimited = '/' . $pattern . '/u';

        return preg_split($delimited, $string, $limit);
    }
}
