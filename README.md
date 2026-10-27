# CODERSTATION String Matching Algorithm

A high-accuracy composite string similarity engine and interactive benchmarking interface built with PHP.

This project combines multiple classic and modern string similarity metrics into an optimized weighted composite score. It is tailored for entity resolution, catalog search queries, typo-tolerant search, and fuzzy matching.

<img width="1915" height="973" alt="image" src="https://github.com/user-attachments/assets/9c30b731-ee85-4871-a869-b40564a48d5e" />

---

## Features

- **Multi-Algorithm Ensemble**: Combines token-based, character-level edit distance, and substring matching.
- **Tuned Weight Distribution**: Calibrated weighting balances token overlap with typographical forgiveness.
- **Zero Heavy Dependencies**: Pure PHP implementation (compatible with PHP 8.0+ and `mbstring`).
- **Interactive Web Interface**: Clean, responsive benchmarking dashboard to test search queries against candidate lists in real-time.
- **Detailed Metric Breakdown**: Displays individual similarity percentages alongside the weighted score and ranking.

---

## Algorithms & Weight Calibration

The composite similarity score is computed via `calculateWeightedStringMatch()` with the following calibrated weighting:

| Algorithm | File | Weight | Primary Purpose |
| :--- | :--- | :---: | :--- |
| **Jaccard Similarity** | [`jaccard.php`](jaccard.php) | **30%** (`0.30`) | Token set intersection over union — excels at word reordering and multigram overlap. |
| **CoderStation Substring Matching** | [`coderstation_substring.php`](coderstation_substring.php) | **30%** (`0.30`) | Direct phrase containment and token coverage (50% word ratio + 50% character ratio). |
| **Damerau-Levenshtein** | [`damerau_levenshtein.php`](damerau_levenshtein.php) | **15%** (`0.15`) | Accounts for insertions, deletions, substitutions, and adjacent transpositions. |
| **Jaro-Winkler** | [`jaro_winkler.php`](jaro_winkler.php) | **15%** (`0.15`) | Emphasizes prefix matches and character transpositions. |
| **Levenshtein Distance** | [`levenshtein.php`](levenshtein.php) | **10%** (`0.10`) | Classic baseline minimum single-character edit distance. |

$$\text{Composite Score} = (0.30 \times J) + (0.30 \times CS) + (0.15 \times DL) + (0.15 \times JW) + (0.10 \times L)$$

---

## Directory Structure

```
├── coderstation_matcher.php    # Composite weighting pipeline
├── coderstation_substring.php  # CoderStation token/character substring coverage algorithm
├── damerau_levenshtein.php     # Damerau-Levenshtein metric implementation
├── favicon.png                 # Application logo / favicon
├── index.php                   # Interactive web testing interface
├── jaccard.php                 # Jaccard similarity implementation
├── jaro_winkler.php            # Jaro-Winkler similarity implementation
├── levenshtein.php             # Levenshtein distance similarity implementation
├── README.md                   # Documentation
└── style.css                   # Custom responsive styling for the web GUI
```

---

## Getting Started

### Prerequisites

- **PHP 8.0** or higher
- `mbstring` extension enabled in `php.ini`
- A local web server (such as Laragon, XAMPP, Apache, Nginx, or PHP's built-in server)

### Running the Web Dashboard

1. Clone or place this repository into your web root (e.g. `f:/Laragon/www/Semantic-Search/string-matching`).
2. If using PHP's built-in web server, run:
   ```bash
   php -S localhost:8000
   ```
3. Open your browser and navigate to:
   ```
   http://localhost:8000/index.php
   ```

---

## Programmatic Usage

You can directly include and call the algorithm in any PHP backend:

```php
<?php
require_once __DIR__ . '/coderstation_matcher.php';

$query = "Apple iPhone 15 Pro Max 256GB";
$candidate = "ihpone 15 pro mix 256gb";

$result = calculateWeightedStringMatch($query, $candidate, caseInsensitive: true);

echo "Candidate: " . $result['text'] . "\n";
echo "Weighted Score: " . $result['weighted_score'] . "\n"; // e.g. 0.8415
echo "Match Percentage: " . $result['percentage'] . "%\n";   // e.g. 84.15%

// Access individual scores
print_r($result['scores']);
/*
Array
(
    [jaccard] => 0.625
    [coderstation_substring] => 0.7917
    [damerau_levenshtein] => 0.88
    [jaro_winkler] => 0.941
    [levenshtein] => 0.88
)
*/
```

---

## Customizing Weights

If your use case places more importance on character typos over token reordering (or vice-versa), you can adjust weights in [`coderstation_matcher.php`](coderstation_matcher.php):

```php
$weights = [
    'jaccard'                => 0.30,
    'coderstation_substring' => 0.30,
    'damerau_levenshtein'    => 0.15,
    'jaro_winkler'           => 0.15,
    'levenshtein'            => 0.10,
];
```

Ensure the sum of all weights equals `1.0`.

---
 
