<?php
/**
 * SLEEVE — Curation (curation.php)
 *
 * "오늘의 한 장" — theme/mood-driven album selection.
 *
 * Filtering is intentionally server-side via the `?mood=` query string so
 * that each mood view has its own shareable URL. The mood value is whitelisted
 * to alphanumeric + hyphen so a malformed query string can never reach SQL.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

// ---------------------------------------------------------------------------
// Input handling
// ---------------------------------------------------------------------------
$selected_mood = isset($_GET['mood']) ? trim($_GET['mood']) : null;
// Whitelist mood to prevent garbage queries — only allow alphanumeric + hyphen.
$selected_mood = preg_match('/^[\w\-]{1,50}$/', $selected_mood ?? '') ? $selected_mood : null;

// ---------------------------------------------------------------------------
// Data
// ---------------------------------------------------------------------------
$albums = get_curated($pdo, $selected_mood);

// Collect all unique moods for the filter pills.
// We need the complete mood universe regardless of which mood is currently selected.
$all_albums_for_moods = get_curated($pdo, null);
$all_moods = [];
foreach ($all_albums_for_moods as $a) {
    foreach ($a['moods'] as $m) {
        $all_moods[$m] = true;
    }
}
$all_moods = array_keys($all_moods);
sort($all_moods);

$page_title = '큐레이션';
require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     Page Hero — "오늘의 한 장"
     ========================================================================= -->
<section class="hero" aria-labelledby="curation-hero-title">
    <div class="hero__inner">
        <span class="hero__eyebrow">Curation</span>
        <h1 id="curation-hero-title" class="hero__title">오늘의 한 장</h1>
        <p class="hero__subtitle">
            무드와 테마로 고르는 바이닐 셀렉션
        </p>
        <hr class="divider-amber divider-amber--center" aria-hidden="true">
    </div>
</section>

<!-- =========================================================================
     Mood Filter — server-side filter via URL
     ========================================================================= -->
<section class="section--tight" aria-label="무드 필터">
    <div class="container-sleeve">
        <div class="filter-bar" role="region" aria-label="무드별 필터">
            <div class="filter-group" role="group" aria-label="무드 선택">
                <span class="filter-group__label">Mood</span>

                <a class="filter-pill<?= $selected_mood === null ? ' active' : '' ?>"
                   href="?"
                   <?= $selected_mood === null ? 'aria-current="page"' : '' ?>>전체</a>

                <?php foreach ($all_moods as $m): ?>
                    <?php $is_active = ($selected_mood !== null && $selected_mood === $m); ?>
                    <a class="filter-pill<?= $is_active ? ' active' : '' ?>"
                       href="?mood=<?= h(urlencode($m)) ?>"
                       <?= $is_active ? 'aria-current="page"' : '' ?>><?= h($m) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     Curated Album Grid
     ========================================================================= -->
<section class="section" aria-labelledby="curation-grid-title">
    <div class="container-sleeve">
        <h2 id="curation-grid-title" class="visually-hidden">큐레이션 음반 목록</h2>

        <?php if ($albums): ?>
            <div class="sleeve-grid sleeve-grid--3">
                <?php foreach ($albums as $album): ?>
                    <article class="sleeve-card">
                        <a href="album.php?slug=<?= h($album['slug']) ?>"
                           style="display:block; color:inherit;"
                           aria-label="<?= h($album['artist']) ?> — <?= h($album['title']) ?>">
                            <div class="sleeve-card__cover">
                                <?php if (!empty($album['is_limited'])): ?>
                                    <span class="badge-limited">한정반</span>
                                <?php endif; ?>
                                <img
                                    src="<?= h($album['cover_path']) ?>"
                                    alt="<?= h($album['artist']) ?> - <?= h($album['title']) ?> 자켓"
                                    loading="lazy"
                                    decoding="async">
                            </div>
                            <div class="sleeve-card__body">
                                <p class="sleeve-card__artist"><?= h($album['artist']) ?></p>
                                <h3 class="sleeve-card__title"><?= h($album['title']) ?></h3>
                                <div class="sleeve-card__meta">
                                    <?php if (!empty($album['release_year'])): ?>
                                        <span><?= h($album['release_year']) ?></span>
                                        <span aria-hidden="true">·</span>
                                    <?php endif; ?>
                                    <span><?= h($album['format']) ?></span>
                                </div>

                                <?php if (!empty($album['moods'])): ?>
                                    <div class="mood-tags" aria-label="이 음반의 무드">
                                        <?php foreach ($album['moods'] as $mood): ?>
                                            <span class="mood-tag"><?= h($mood) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted-custom" style="text-align:center; padding:var(--space-7) 0;">
                선택한 무드의 음반이 없습니다.
            </p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
