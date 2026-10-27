<?php

/**
 * Calculates Jaro Distance between two strings.
 * Returns a value between 0.0 and 1.0.
 *
 * @param string $s1
 * @param string $s2
 * @return float
 */
function jaroDistance(string $s1, string $s2): float
{
    $len1 = mb_strlen($s1, 'UTF-8');
    $len2 = mb_strlen($s2, 'UTF-8');

    if ($len1 === 0 && $len2 === 0) {
        return 1.0;
    }
    if ($len1 === 0 || $len2 === 0) {
        return 0.0;
    }

    $chars1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
    $chars2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);

    // Matching window distance
    $matchDistance = (int) floor(max($len1, $len2) / 2) - 1;
    if ($matchDistance < 0) {
        $matchDistance = 0;
    }

    $s1Matches = array_fill(0, $len1, false);
    $s2Matches = array_fill(0, $len2, false);

    $matches = 0;
    for ($i = 0; $i < $len1; $i++) {
        $start = max(0, $i - $matchDistance);
        $end = min($i + $matchDistance + 1, $len2);

        for ($j = $start; $j < $end; $j++) {
            if ($s2Matches[$j]) {
                continue;
            }
            if ($chars1[$i] !== $chars2[$j]) {
                continue;
            }
            $s1Matches[$i] = true;
            $s2Matches[$j] = true;
            $matches++;
            break;
        }
    }

    if ($matches === 0) {
        return 0.0;
    }

    // Count transpositions
    $k = 0;
    $transpositions = 0;
    for ($i = 0; $i < $len1; $i++) {
        if (!$s1Matches[$i]) {
            continue;
        }
        while (!$s2Matches[$k]) {
            $k++;
        }
        if ($chars1[$i] !== $chars2[$k]) {
            $transpositions++;
        }
        $k++;
    }

    $halfTranspositions = $transpositions / 2.0;

    return (($matches / $len1) + ($matches / $len2) + (($matches - $halfTranspositions) / $matches)) / 3.0;
}

/**
 * Calculates Jaro-Winkler Similarity with prefix scaling.
 *
 * @param string $s1
 * @param string $s2
 * @param float $scalingFactor Default standard prefix scale is 0.1
 * @param int $maxPrefixLength Maximum prefix length considered (standard is 4)
 * @param bool $caseInsensitive
 * @return float
 */
function jaroWinklerSimilarity(
    string $s1,
    string $s2,
    float $scalingFactor = 0.1,
    int $maxPrefixLength = 4,
    bool $caseInsensitive = true
): float {
    if ($caseInsensitive) {
        $s1 = mb_strtolower($s1, 'UTF-8');
        $s2 = mb_strtolower($s2, 'UTF-8');
    }

    $jaro = jaroDistance($s1, $s2);

    $chars1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
    $chars2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);

    $minLen = min(count($chars1), count($chars2), $maxPrefixLength);
    $prefix = 0;

    for ($i = 0; $i < $minLen; $i++) {
        if ($chars1[$i] === $chars2[$i]) {
            $prefix++;
        } else {
            break;
        }
    }

    return $jaro + ($prefix * $scalingFactor * (1.0 - $jaro));
}
