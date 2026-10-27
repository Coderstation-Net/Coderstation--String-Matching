<?php

/**
 * Calculates Damerau-Levenshtein distance (optimal string alignment metric),
 * which supports insertions, deletions, substitutions, and transpositions.
 *
 * @param string $s1
 * @param string $s2
 * @return int
 */
function damerauLevenshteinDistance(string $s1, string $s2): int
{
    $len1 = mb_strlen($s1, 'UTF-8');
    $len2 = mb_strlen($s2, 'UTF-8');

    if ($len1 === 0) {
        return $len2;
    }
    if ($len2 === 0) {
        return $len1;
    }

    $chars1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
    $chars2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);

    $d = [];

    for ($i = 0; $i <= $len1; $i++) {
        $d[$i][0] = $i;
    }
    for ($j = 0; $j <= $len2; $j++) {
        $d[0][$j] = $j;
    }

    for ($i = 1; $i <= $len1; $i++) {
        for ($j = 1; $j <= $len2; $j++) {
            $cost = ($chars1[$i - 1] === $chars2[$j - 1]) ? 0 : 1;

            $d[$i][$j] = min(
                $d[$i - 1][$j] + 1,       // Deletion
                $d[$i][$j - 1] + 1,       // Insertion
                $d[$i - 1][$j - 1] + $cost // Substitution
            );

            // Transposition check
            if (
                $i > 1 && $j > 1
                && $chars1[$i - 1] === $chars2[$j - 2]
                && $chars1[$i - 2] === $chars2[$j - 1]
            ) {
                $d[$i][$j] = min($d[$i][$j], $d[$i - 2][$j - 2] + 1);
            }
        }
    }

    return $d[$len1][$len2];
}

/**
 * Normalizes Damerau-Levenshtein distance into a similarity score between 0.0 and 1.0.
 *
 * @param string $s1
 * @param string $s2
 * @param bool $caseInsensitive
 * @return float
 */
function damerauLevenshteinSimilarity(string $s1, string $s2, bool $caseInsensitive = true): float
{
    if ($caseInsensitive) {
        $s1 = mb_strtolower($s1, 'UTF-8');
        $s2 = mb_strtolower($s2, 'UTF-8');
    }

    $maxLen = max(mb_strlen($s1, 'UTF-8'), mb_strlen($s2, 'UTF-8'));
    if ($maxLen === 0) {
        return 1.0;
    }

    $dist = damerauLevenshteinDistance($s1, $s2);
    return max(0.0, 1.0 - ($dist / $maxLen));
}
