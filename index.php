<?php
require_once __DIR__ . '/coderstation_matcher.php';

$defaultQuery = "Apple iPhone 15 Pro Max 256GB";
$defaultCandidates = implode("\n", [
    "Apple iPhone 15 Pro Max 256GB Natural Titanium",
    "iPhone 15 Pro Max",
    "ihpone 15 pro mix 256gb",
    "Samsung Galaxy S24 Ultra 256GB",
    "Sony PlayStation 5 Digital Edition"
]);

$query = isset($_POST['query']) ? trim($_POST['query']) : $defaultQuery;
$candidatesRaw = isset($_POST['candidates']) ? trim($_POST['candidates']) : $defaultCandidates;

$results = [];
if (!empty($query) && !empty($candidatesRaw)) {
    $lines = array_filter(array_map('trim', explode("\n", $candidatesRaw)));
    foreach ($lines as $line) {
        if ($line === '')
            continue;
        $results[] = calculateWeightedStringMatch($query, $line);
    }

    // Sort by highest weighted score
    usort($results, fn($a, $b) => $b['weighted_score'] <=> $a['weighted_score']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>String Matching Algorithm</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Header (Height: 38px) -->
    <header class="app-topbar">
        <div class="topbar-brand">
            <img src="favicon.png" alt="Logo" class="topbar-brand-logo">
            String Matching Algorithm
        </div>
        <div class="topbar-tag">
            <img src="favicon.png" alt="Icon" class="topbar-tag-icon">
            <span>CODERSTATION</span>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="layout-grid">

                <!-- Input Form -->
                <div class="panel">
                    <h2 class="panel-title">
                        <i class="fa-solid fa-keyboard"></i>
                        Test Inputs
                    </h2>

                    <form method="POST">
                        <div class="form-group">
                            <label for="query">Target Search Query</label>
                            <input type="text" id="query" name="query" value="<?= htmlspecialchars($query) ?>" required
                                placeholder="Enter query...">
                        </div>

                        <div class="form-group">
                            <label for="candidates">Candidates (One per line)</label>
                            <textarea id="candidates" name="candidates" rows="7" required
                                placeholder="Enter candidate strings..."><?= htmlspecialchars($candidatesRaw) ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-calculator"></i>
                            Compute Match Scores
                        </button>
                    </form>
                </div>

                <!-- Output / Results Table -->
                <div class="panel">
                    <h2 class="panel-title">
                        <i class="fa-solid fa-ranking-star"></i>
                        Ranked Results (<?= count($results) ?>)
                    </h2>

                    <?php if (empty($results)): ?>
                        <p style="color: #9ca3af; text-align: center; padding: 1.5rem 0;">No results to display. Run a
                            search query.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="results-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 42px;">Rank</th>
                                        <th>Candidate Text</th>
                                        <th class="text-right">Jaccard (30%)</th>
                                        <th class="text-right">Substring (30%)</th>
                                        <th class="text-right">Damerau-Levenshtein (15%)</th>
                                        <th class="text-right">Jaro-Winkler (15%)</th>
                                        <th class="text-right">Levenshtein (10%)</th>
                                        <th class="text-right" style="width: 80px;">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $index => $res): ?>
                                        <?php
                                        $s = $res['scores'];
                                        $rankClass = ($index === 0) ? 'rank-top1' : '';
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="rank-badge <?= $rankClass ?>">#<?= $index + 1 ?></span>
                                            </td>
                                            <td>
                                                <span class="candidate-name"><?= htmlspecialchars($res['text']) ?></span>
                                            </td>
                                            <td class="text-right metric-cell color-jaccard">
                                                <div class="metric-val-wrap">
                                                    <span
                                                        class="metric-num"><?= number_format($s['jaccard'] * 100, 1) ?>%</span>
                                                    <div class="line-progress">
                                                        <div class="line-bar bar-jaccard"
                                                            style="width: <?= min(100, $s['jaccard'] * 100) ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right metric-cell color-substr">
                                                <div class="metric-val-wrap">
                                                    <span
                                                        class="metric-num"><?= number_format($s['coderstation_substring'] * 100, 1) ?>%</span>
                                                    <div class="line-progress">
                                                        <div class="line-bar bar-substr"
                                                            style="width: <?= min(100, $s['substring'] * 100) ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right metric-cell color-damerau">
                                                <div class="metric-val-wrap">
                                                    <span
                                                        class="metric-num"><?= number_format($s['damerau_levenshtein'] * 100, 1) ?>%</span>
                                                    <div class="line-progress">
                                                        <div class="line-bar bar-damerau"
                                                            style="width: <?= min(100, $s['damerau_levenshtein'] * 100) ?>%;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right metric-cell color-jaro">
                                                <div class="metric-val-wrap">
                                                    <span
                                                        class="metric-num"><?= number_format($s['jaro_winkler'] * 100, 1) ?>%</span>
                                                    <div class="line-progress">
                                                        <div class="line-bar bar-jaro"
                                                            style="width: <?= min(100, $s['jaro_winkler'] * 100) ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right metric-cell color-lev">
                                                <div class="metric-val-wrap">
                                                    <span
                                                        class="metric-num"><?= number_format($s['levenshtein'] * 100, 1) ?>%</span>
                                                    <div class="line-progress">
                                                        <div class="line-bar bar-lev"
                                                            style="width: <?= min(100, $s['levenshtein'] * 100) ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right total-cell">
                                                <div class="total-val-wrap">
                                                    <span class="total-num"><?= number_format($res['percentage'], 2) ?>%</span>
                                                    <div class="line-progress total-progress">
                                                        <div class="line-bar bar-total"
                                                            style="width: <?= min(100, $res['percentage']) ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Matching Techniques Reference Container -->
            <div class="panel info-panel">
                <div class="table-responsive">
                    <table class="info-table">
                        <thead>
                            <tr>
                                <th style="width: 220px;">Matching Technique</th>
                                <th style="width: 290px;">Author</th>
                                <th class="text-right" style="width: 90px;">Weight</th>
                                <th>Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="tech-badge">
                                        <span class="chip-dot dot-jaccard"></span>
                                        Jaccard Similarity
                                    </span>
                                </td>
                                <td class="author-text">Paul Jaccard (1901)</td>
                                <td class="text-right metric-cell color-jaccard">30%</td>
                                <td class="function-text">Measures the overlap between unique words in the query and
                                    text.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="tech-badge">
                                        <span class="chip-dot dot-substr"></span>
                                        Substring Matching
                                    </span>
                                </td>
                                <td class="author-text">Coderstation's customized String-Matching Algorithm (2024)</td>
                                <td class="text-right metric-cell color-substr">30%</td>
                                <td class="function-text">Checks whether exact words or phrases appear within the text.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="tech-badge">
                                        <span class="chip-dot dot-damerau"></span>
                                        Damerau-Levenshtein Distance
                                    </span>
                                </td>
                                <td class="author-text">Fred J. Damerau (1964); later combined with Levenshtein's
                                    edit-distance framework</td>
                                <td class="text-right metric-cell color-damerau">15%</td>
                                <td class="function-text">Handles insertions, deletions, substitutions, and adjacent
                                    character transpositions.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="tech-badge">
                                        <span class="chip-dot dot-jaro"></span>
                                        Jaro-Winkler Similarity
                                    </span>
                                </td>
                                <td class="author-text">Matthew A. Jaro (1989); William E. Winkler (1990)</td>
                                <td class="text-right metric-cell color-jaro">15%</td>
                                <td class="function-text">Identifies similar strings, with additional emphasis on
                                    matching prefixes.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="tech-badge">
                                        <span class="chip-dot dot-lev"></span>
                                        Levenshtein Distance
                                    </span>
                                </td>
                                <td class="author-text">Vladimir I. Levenshtein (1965)</td>
                                <td class="text-right metric-cell color-lev">10%</td>
                                <td class="function-text">Measures the minimum number of character insertions,
                                    deletions, and substitutions needed to transform one string into another.</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>Total</strong></td>
                                <td class="text-right total-cell">100%</td>
                                <td class="function-text"><strong>Combined weighted similarity score</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (Height: 28px) -->
    <footer class="app-footer">
        <div class="footer-content">
            <span class="footer-copy">&copy; Copyright <span class="footer-brand">CODERSTATION</span> 2025. All Rights Reserved.</span>
        </div>
    </footer>

</body>

</html>