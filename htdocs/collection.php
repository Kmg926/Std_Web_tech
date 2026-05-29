<?php
/**
 * SLEEVE — Collection (collection.php)
 *
 * Renders the full album archive with client-side filtering (genre/format/year).
 * The DOM contract here is the same one main.js reads:
 *
 *   .filter-bar[data-filter-bar]
 *     .filter-group
 *       .filter-pill[data-filter-group][data-filter-value][aria-pressed]
 *
 *   [data-filter-target]
 *     .sleeve-card[data-genre][data-format][data-year]
 *
 *   [data-filter-empty][hidden]
 *
 * We also expose a [data-count-display] span which main.js updates with the
 * current visible card count.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo        = db();
$page_title = '컬렉션';

// Fetch everything; JS handles filtering client-side so users get instant feedback.
$albums = get_albums($pdo, []);

// Build filter pill sets — unique, sorted, NULL/empty stripped.
$genres  = array_values(array_unique(array_filter(array_column($albums, 'genre'),  static fn($g) => $g !== null && $g !== '')));
$formats = array_values(array_unique(array_filter(array_column($albums, 'format'), static fn($f) => $f !== null && $f !== '')));
$years   = array_values(array_unique(array_filter(array_column($albums, 'release_year'), static fn($y) => $y !== null && $y !== '')));

sort($genres,  SORT_STRING);
sort($formats, SORT_STRING);
rsort($years,  SORT_NUMERIC);

$total_count = count($albums);

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" aria-labelledby="collection-title">
    <div class="container-sleeve">

        <!-- ==============================================================
             Header
             ============================================================== -->
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Archive</span>
                <h1 id="collection-title">컬렉션</h1>
                <p class="text-muted-custom" style="margin-top: var(--space-2);">
                    보유 음반 아카이브 ·
                    총 <span data-count-display><?= h($total_count) ?></span>장
                </p>
            </div>
        </header>

        <!-- ==============================================================
             Filter Bar — DOM contract for main.js
             ============================================================== -->
        <div class="filter-bar" data-filter-bar role="region" aria-label="컬렉션 필터">

            <div class="filter-group" role="group" aria-label="장르 필터">
                <span class="filter-group__label">장르</span>
                <button type="button"
                        class="filter-pill active"
                        data-filter-group="genre"
                        data-filter-value="all"
                        aria-pressed="true">전체</button>
                <?php foreach ($genres as $genre): ?>
                    <button type="button"
                            class="filter-pill"
                            data-filter-group="genre"
                            data-filter-value="<?= h($genre) ?>"
                            aria-pressed="false"><?= h($genre) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="filter-group" role="group" aria-label="포맷 필터">
                <span class="filter-group__label">포맷</span>
                <button type="button"
                        class="filter-pill active"
                        data-filter-group="format"
                        data-filter-value="all"
                        aria-pressed="true">전체</button>
                <?php foreach ($formats as $format): ?>
                    <button type="button"
                            class="filter-pill"
                            data-filter-group="format"
                            data-filter-value="<?= h($format) ?>"
                            aria-pressed="false"><?= h($format) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="filter-group" role="group" aria-label="연도 필터">
                <span class="filter-group__label">연도</span>
                <button type="button"
                        class="filter-pill active"
                        data-filter-group="year"
                        data-filter-value="all"
                        aria-pressed="true">전체</button>
                <?php foreach ($years as $year): ?>
                    <button type="button"
                            class="filter-pill"
                            data-filter-group="year"
                            data-filter-value="<?= h($year) ?>"
                            aria-pressed="false"><?= h($year) ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==============================================================
             Filter Target — cards container
             ============================================================== -->
        <?php if ($albums): ?>
            <div class="sleeve-grid" data-filter-target>
                <?php foreach ($albums as $album): ?>
                    <article class="sleeve-card"
                             data-genre="<?= h($album['genre']) ?>"
                             data-format="<?= h($album['format']) ?>"
                             data-year="<?= h($album['release_year']) ?>">
                        <a href="album.php?slug=<?= h($album['slug']) ?>"
                           style="display:block; color:inherit;"
                           aria-label="<?= h($album['artist']) ?> — <?= h($album['title']) ?>">
                            <div class="sleeve-card__cover">
                                <?php if (!empty($album['is_limited'])): ?>
                                    <span class="badge-limited">한정반</span>
                                <?php endif; ?>
                                <img
                                    data-src="<?= h($album['cover_path']) ?>"
                                    src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"
                                    alt="<?= h($album['artist']) ?> - <?= h($album['title']) ?> 자켓"
                                    loading="lazy">
                            </div>
                            <div class="sleeve-card__body">
                                <p class="sleeve-card__artist"><?= h($album['artist']) ?></p>
                                <h3 class="sleeve-card__title"><?= h($album['title']) ?></h3>
                                <div class="sleeve-card__meta">
                                    <span><?= h($album['release_year']) ?></span>
                                    <span aria-hidden="true">·</span>
                                    <span><?= h($album['format']) ?></span>
                                    <?php if (!empty($album['genre'])): ?>
                                        <span aria-hidden="true">·</span>
                                        <span><?= h($album['genre']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <p data-filter-empty hidden class="text-muted-custom" style="text-align:center; padding:var(--space-7) 0;">
                일치하는 결과가 없습니다.
            </p>
        <?php else: ?>
            <p class="text-muted-custom" style="text-align:center; padding:var(--space-7) 0;">
                아직 등록된 음반이 없습니다.
            </p>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
