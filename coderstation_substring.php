<?php

/**
 * Checks substring coverage of query words within the target text.
 * Calculates the proportion of words (and characters) from the query that appear explicitly inside the text.
 * Returns a score between 0.0 and 1.0.
 *
 * @param string $query
 * @param string $text
 * @param bool $caseInsensitive
 * @return float
 */
function coderstationSubstringMatching(string $query, string $text, bool $caseInsensitive = true): float
{
    if ($caseInsensitive) {
        $query = mb_strtolower($query, 'UTF-8');
        $text = mb_strtolower($text, 'UTF-8');
    }

    $trimmedQuery = trim($query);
    $trimmedText = trim($text);

    if ($trimmedQuery === '' && $trimmedText === '') {
        return 1.0;
    }

    if ($trimmedQuery === '' || $trimmedText === '') {
        return 0.0;
    }

    // Direct whole substring match
    if (str_contains($text, $query)) {
        return 1.0;
    }

    preg_match_all('/[\p{L}\p{N}]+/u', $query, $queryMatches);
    $words = $queryMatches[0] ?? [];

    if (empty($words)) {
        return 0.0;
    }

    $matchedWords = 0;
    $totalChars = 0;
    $matchedChars = 0;

    foreach ($words as $word) {
        $wordLen = mb_strlen($word, 'UTF-8');
        $totalChars += $wordLen;

        if (str_contains($text, $word)) {
            $matchedWords++;
            $matchedChars += $wordLen;
        }
    }

    // Blend word presence ratio (50%) and character presence ratio (50%)
    $wordRatio = $matchedWords / count($words);
    $charRatio = $totalChars > 0 ? ($matchedChars / $totalChars) : 0.0;

    return (float) (($wordRatio * 0.5) + ($charRatio * 0.5));
}
