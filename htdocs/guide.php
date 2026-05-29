<?php
/**
 * SLEEVE — Vinyl Guide (guide.php)
 *
 * Editorial guide hub. Categories are filtered server-side via `?category=`
 * against a fixed whitelist so the DB only ever sees a known value.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

// ---------------------------------------------------------------------------
// Input handling
// ---------------------------------------------------------------------------
$selected_cat = isset($_GET['category']) ? trim($_GET['category']) : null;
$allowed_cats = ['beginner', 'care', 'equipment', 'culture'];
$selected_cat = in_array($selected_cat, $allowed_cats, true) ? $selected_cat : null;

// Korean labels for the category keys — kept in one place so the badge,
// the tab label, and any future references stay in sync.
$category_labels = [
    'beginner'  => '입문',
    'care'      => '관리',
    'equipment' => '장비',
    'culture'   => '문화',
];

// ---------------------------------------------------------------------------
// Data
// ---------------------------------------------------------------------------
$posts = get_guide_posts($pdo, $selected_cat);

$page_title = '바이닐 가이드';
require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     Hero
     ========================================================================= -->
<section class="hero" aria-labelledby="guide-hero-title">
    <div class="hero__inner">
        <span class="hero__eyebrow">Guide</span>
        <h1 id="guide-hero-title" class="hero__title">바이닐 가이드</h1>
        <p class="hero__subtitle">
            LP를 처음 시작하는 분들을 위한 안내
        </p>
        <hr class="divider-amber divider-amber--center" aria-hidden="true">
    </div>
</section>

<!-- =========================================================================
     Category Tabs — server-side filter
     ========================================================================= -->
<section class="section--tight" aria-label="카테고리 필터">
    <div class="container-sleeve">
        <div class="filter-bar" role="region" aria-label="카테고리별 필터">
            <div class="filter-group" role="group" aria-label="가이드 카테고리">
                <span class="filter-group__label">Category</span>

                <a class="filter-pill<?= $selected_cat === null ? ' active' : '' ?>"
                   href="?"
                   <?= $selected_cat === null ? 'aria-current="page"' : '' ?>>전체</a>

                <?php foreach ($category_labels as $key => $label): ?>
                    <?php $is_active = ($selected_cat === $key); ?>
                    <a class="filter-pill<?= $is_active ? ' active' : '' ?>"
                       href="?category=<?= h(urlencode($key)) ?>"
                       <?= $is_active ? 'aria-current="page"' : '' ?>><?= h($label) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     Post List
     ========================================================================= -->
<section class="section" aria-labelledby="guide-list-title">
    <div class="container-sleeve">
        <h2 id="guide-list-title" class="visually-hidden">가이드 게시글 목록</h2>

        <?php if ($posts): ?>
            <div class="guide-list">
                <?php foreach ($posts as $post): ?>
                    <?php
                    $cat_key   = (string)$post['category'];
                    $cat_label = $category_labels[$cat_key] ?? $cat_key;

                    // Strip HTML before truncating so we never break a tag.
                    $plain     = strip_tags((string)$post['body']);
                    $excerpt   = mb_strlen($plain, 'UTF-8') > 120
                        ? mb_substr($plain, 0, 120, 'UTF-8') . '...'
                        : $plain;

                    $created_ts   = !empty($post['created_at']) ? strtotime($post['created_at']) : false;
                    $created_disp = $created_ts ? date('Y.m.d', $created_ts) : '';
                    $created_iso  = $created_ts ? date('Y-m-d', $created_ts) : '';
                    ?>
                    <article class="guide-post">
                        <div class="guide-post__main">
                            <h3 class="guide-post__title">
                                <a href="guide-post.php?slug=<?= h($post['slug']) ?>"
                                   style="color:inherit;">
                                    <?= h($post['title']) ?>
                                </a>
                            </h3>
                            <p class="guide-post__excerpt"><?= h($excerpt) ?></p>
                        </div>

                        <div class="guide-post__aside">
                            <span class="category-badge"><?= h($cat_label) ?></span>
                            <?php if ($created_disp !== ''): ?>
                                <time class="guide-post__date" datetime="<?= h($created_iso) ?>">
                                    <?= h($created_disp) ?>
                                </time>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted-custom" style="text-align:center; padding:var(--space-7) 0;">
                작성된 게시글이 없습니다.
            </p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
