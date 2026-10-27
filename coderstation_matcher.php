<?php

require_once __DIR__ . '/jaccard.php';
require_once __DIR__ . '/coderstation_substring.php';
require_once __DIR__ . '/damerau_levenshtein.php';
require_once __DIR__ . '/jaro_winkler.php';
require_once __DIR__ . '/levenshtein.php';

/**
 * Computes the weighted composite similarity score between query and text.
 * Weights:
 * - Jaccard Similarity: 30% (0.30)
 * - CoderStation Substring Matching: 30% (0.30)
 * - Damerau-Levenshtein: 15% (0.15)
 * - Jaro-Winkler: 15% (0.15)
 * - Levenshtein: 10% (0.10)
 *
 * @param string $query
 * @param string $text
 * @param bool $caseInsensitive
 * @return array{
 *     query: string,
 *     text: string,
 *     scores: array{
 *         jaccard: float,
 *         substring: float,
 *         damerau_levenshtein: float,
 *         jaro_winkler: float,
 *         levenshtein: float
 *     },
 *     weights: array{
 *         jaccard: float,
 *         substring: float,
 *         damerau_levenshtein: float,
 *         jaro_winkler: float,
 *         levenshtein: float
 *     },
 *     weighted_score: float,
 *     percentage: float
 * }
 */
function calculateWeightedStringMatch(string $query, string $text, bool $caseInsensitive = true): array
{
    $weights = [
        'jaccard' => 0.30,
        'coderstation_substring' => 0.30,
        'damerau_levenshtein' => 0.15,
        'jaro_winkler' => 0.15,
        'levenshtein' => 0.10,
    ];

    $jaccard = jaccardSimilarity($query, $text, $caseInsensitive);
    $coderstation_substring = coderstationSubstringMatching($query, $text, $caseInsensitive);
    $damerau = damerauLevenshteinSimilarity($query, $text, $caseInsensitive);
    $jaroWinkler = jaroWinklerSimilarity($query, $text, 0.1, 4, $caseInsensitive);
    $levenshtein = levenshteinSimilarity($query, $text, $caseInsensitive);

    $weightedScore = (
        ($jaccard * $weights['jaccard']) +
        ($coderstation_substring * $weights['coderstation_substring']) +
        ($damerau * $weights['damerau_levenshtein']) +
        ($jaroWinkler * $weights['jaro_winkler']) +
        ($levenshtein * $weights['levenshtein'])
    );

    return [
        'query' => $query,
        'text' => $text,
        'scores' => [
            'jaccard' => round($jaccard, 4),
            'coderstation_substring' => round($coderstation_substring, 4),
            'damerau_levenshtein' => round($damerau, 4),
            'jaro_winkler' => round($jaroWinkler, 4),
            'levenshtein' => round($levenshtein, 4),
        ],
        'weights' => $weights,
        'weighted_score' => round($weightedScore, 4),
        'percentage' => round($weightedScore * 100, 2),
    ];
}
