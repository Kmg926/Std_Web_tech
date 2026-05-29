<?php
/**
 * SLEEVE — Collection Stats (stats.php)
 *
 * Aggregate metrics for the archive: totals, genre histogram, year histogram,
 * and a Vinyl-vs-CD format split with a CSS-only proportion bar.
 *
 * Charts are drawn by main.js's lightweight canvas helper:
 *   SLEEVE.renderBarChart(canvasId, labels, data, color)
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo   = db();
$stats = get_stats($pdo);

// ---------------------------------------------------------------------------
// Derived values for the format-split section
// ---------------------------------------------------------------------------
$total_vinyl = (int)($stats['total_vinyl'] ?? 0);
$total_cd    = (int)($stats['total_cd'] ?? 0);
$total_fmt   = $total_vinyl + $total_cd;

// Guard against division by zero when the archive is empty.
$vinyl_pct = $total_fmt > 0 ? round(($total_vinyl / $total_fmt) * 100, 1) : 0;
$cd_pct    = $total_fmt > 0 ? round(($total_cd    / $total_fmt) * 100, 1) : 0;

$page_title = '컬렉션 통계';
require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     Hero
     ========================================================================= -->
<section class="hero" aria-labelledby="stats-hero-title">
    <div class="hero__inner">
        <span class="hero__eyebrow">Stats</span>
        <h1 id="stats-hero-title" class="hero__title">컬렉션 통계</h1>
        <p class="hero__subtitle">
            아카이브 전체의 숫자와 분포를 한눈에
        </p>
        <hr class="divider-amber divider-amber--center" aria-hidden="true">
    </div>
</section>

<!-- =========================================================================
     Summary Cards — totals
     ========================================================================= -->
<section class="section" aria-labelledby="stats-summary-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Summary</span>
                <h2 id="stats-summary-title">전체 현황</h2>
            </div>
        </header>

        <div class="stats-grid">
            <article class="stats-card" aria-labelledby="sum-total">
                <p id="sum-total" class="stats-card__title">전체 음반</p>
                <span class="stat-number"><?= h(number_format((int)$stats['total_albums'])) ?></span>
                <span class="stat-label"><?= h(number_format((int)$stats['total_albums'])) ?>장</span>
            </article>

            <article class="stats-card" aria-labelledby="sum-vinyl">
                <p id="sum-vinyl" class="stats-card__title">바이닐</p>
                <span class="stat-number"><?= h(number_format($total_vinyl)) ?></span>
                <span class="stat-label"><?= h(number_format($total_vinyl)) ?> LP</span>
            </article>

            <article class="stats-card" aria-labelledby="sum-cd">
                <p id="sum-cd" class="stats-card__title">CD</p>
                <span class="stat-number"><?= h(number_format($total_cd)) ?></span>
                <span class="stat-label"><?= h(number_format($total_cd)) ?> CD</span>
            </article>
        </div>
    </div>
</section>

<!-- =========================================================================
     장르 분포 — Bar chart
     ========================================================================= -->
<section class="section bg-surface" aria-labelledby="stats-genre-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Distribution</span>
                <h2 id="stats-genre-title">장르 분포</h2>
            </div>
        </header>

        <article class="stats-card">
            <?php if (!empty($stats['genres'])): ?>
                <div class="stats-card__chart">
                    <canvas id="genreChart"
                            role="img"
                            aria-label="장르별 음반 수를 보여주는 막대 그래프"></canvas>
                </div>
            <?php else: ?>
                <p class="text-muted-custom" style="text-align:center; padding:var(--space-6) 0;">
                    표시할 장르 데이터가 없습니다.
                </p>
            <?php endif; ?>
        </article>
    </div>
</section>

<!-- =========================================================================
     연도별 분포 — Bar chart
     ========================================================================= -->
<section class="section" aria-labelledby="stats-year-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Timeline</span>
                <h2 id="stats-year-title">연도별 분포</h2>
            </div>
        </header>

        <article class="stats-card">
            <?php if (!empty($stats['years'])): ?>
                <div class="stats-card__chart">
                    <canvas id="yearChart"
                            role="img"
                            aria-label="발매 연도별 음반 수를 보여주는 막대 그래프"></canvas>
                </div>
            <?php else: ?>
                <p class="text-muted-custom" style="text-align:center; padding:var(--space-6) 0;">
                    표시할 연도 데이터가 없습니다.
                </p>
            <?php endif; ?>
        </article>
    </div>
</section>

<!-- =========================================================================
     포맷 비율 — Vinyl vs CD
     ========================================================================= -->
<section class="section bg-surface" aria-labelledby="stats-format-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Format Split</span>
                <h2 id="stats-format-title">포맷 비율</h2>
            </div>
        </header>

        <div class="stats-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
            <article class="stats-card" aria-labelledby="fmt-vinyl">
                <p id="fmt-vinyl" class="stats-card__title">바이닐</p>
                <span class="stat-number"><?= h(number_format($total_vinyl)) ?></span>
                <span class="stat-label"><?= h($vinyl_pct) ?>%</span>

                <div role="progressbar"
                     aria-valuenow="<?= h($vinyl_pct) ?>"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     aria-label="바이닐 비율"
                     style="
                        margin-top: var(--space-4);
                        width: 100%;
                        height: 8px;
                        background-color: rgba(226, 216, 200, 0.08);
                        border-radius: var(--radius-pill);
                        overflow: hidden;
                     ">
                    <div style="
                        width: <?= h($vinyl_pct) ?>%;
                        height: 100%;
                        background-color: var(--amber);
                        transition: width var(--transition-base);
                     "></div>
                </div>
            </article>

            <article class="stats-card" aria-labelledby="fmt-cd">
                <p id="fmt-cd" class="stats-card__title">CD</p>
                <span class="stat-number"><?= h(number_format($total_cd)) ?></span>
                <span class="stat-label"><?= h($cd_pct) ?>%</span>

                <div role="progressbar"
                     aria-valuenow="<?= h($cd_pct) ?>"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     aria-label="CD 비율"
                     style="
                        margin-top: var(--space-4);
                        width: 100%;
                        height: 8px;
                        background-color: rgba(226, 216, 200, 0.08);
                        border-radius: var(--radius-pill);
                        overflow: hidden;
                     ">
                    <div style="
                        width: <?= h($cd_pct) ?>%;
                        height: 100%;
                        background-color: var(--teal);
                        transition: width var(--transition-base);
                     "></div>
                </div>
            </article>
        </div>
    </div>
</section>

<?php
// -----------------------------------------------------------------------------
// JSON output for inline <script>.
// JSON_HEX_TAG/APOS/QUOT/AMP prevent XSS via DB-supplied genre or year strings
// that could otherwise break out of the <script> context (e.g. a genre value
// containing "</script><svg onload=...>").
// -----------------------------------------------------------------------------
$json_script_flags = JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
?>
<?php if (!empty($stats['genres']) || !empty($stats['years'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.SLEEVE || typeof window.SLEEVE.renderBarChart !== 'function') return;

    <?php if (!empty($stats['genres'])): ?>
    SLEEVE.renderBarChart(
        'genreChart',
        <?= json_encode(array_keys($stats['genres']),   $json_script_flags) ?>,
        <?= json_encode(array_values($stats['genres']), $json_script_flags) ?>,
        '#D98A3D'
    );
    <?php endif; ?>

    <?php if (!empty($stats['years'])): ?>
    SLEEVE.renderBarChart(
        'yearChart',
        <?= json_encode(array_map('strval', array_keys($stats['years'])), $json_script_flags) ?>,
        <?= json_encode(array_values($stats['years']),                    $json_script_flags) ?>,
        '#5C8374'
    );
    <?php endif; ?>

    // Re-render on resize so charts stay crisp at any breakpoint.
    window.SLEEVE.onChartResize = function () {
        <?php if (!empty($stats['genres'])): ?>
        SLEEVE.renderBarChart(
            'genreChart',
            <?= json_encode(array_keys($stats['genres']),   $json_script_flags) ?>,
            <?= json_encode(array_values($stats['genres']), $json_script_flags) ?>,
            '#D98A3D'
        );
        <?php endif; ?>
        <?php if (!empty($stats['years'])): ?>
        SLEEVE.renderBarChart(
            'yearChart',
            <?= json_encode(array_map('strval', array_keys($stats['years'])), $json_script_flags) ?>,
            <?= json_encode(array_values($stats['years']),                    $json_script_flags) ?>,
            '#5C8374'
        );
        <?php endif; ?>
    };
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
