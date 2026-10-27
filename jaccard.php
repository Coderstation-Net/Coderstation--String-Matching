<?php

/**
 * Calculates Jaccard Similarity between two strings based on token/word sets.
 * Formula: |A ∩ B| / |A ∪ B|
 * Returns a score between 0.0 and 1.0.
 *
 * @param string $query
 * @param string $text
 * @param bool $caseInsensitive
 * @return float
 */
function jaccardSimilarity(string $query, string $text, bool $caseInsensitive = true): float
{
    if ($caseInsensitive) {
        $query = mb_strtolower($query, 'UTF-8');
        $text = mb_strtolower($text, 'UTF-8');
    }

    // Tokenize words using unicode word characters
    preg_match_all('/[\p{L}\p{N}]+/u', $query, $queryMatches);
    preg_match_all('/[\p{L}\p{N}]+/u', $text, $textMatches);

    $queryTokens = array_unique($queryMatches[0] ?? []);
    $textTokens = array_unique($textMatches[0] ?? []);

    if (empty($queryTokens) && empty($textTokens)) {
        return 1.0;
    }

    if (empty($queryTokens) || empty($textTokens)) {
        return 0.0;
    }

    $intersection = array_intersect($queryTokens, $textTokens);
    $union = array_unique(array_merge($queryTokens, $textTokens));

    $unionCount = count($union);
    if ($unionCount === 0) {
        return 0.0;
    }

    return (float) (count($intersection) / $unionCount);
}
