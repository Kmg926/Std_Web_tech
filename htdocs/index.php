<?php
/**
 * SLEEVE — Home (index.php)
 *
 * Landing page: hero + recent albums + recent designs + culture intro.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo        = db();
$page_title = '홈';

$recent_albums = get_albums($pdo, []);
$recent_albums = array_slice($recent_albums, 0, 4);

$recent_designs = get_designs($pdo);
$recent_designs = array_slice($recent_designs, 0, 3);

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     Hero
     ========================================================================= -->
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__inner">
        <div class="vinyl-ring" aria-hidden="true" style="margin: 0 auto var(--space-6);"></div>

        <span class="hero__eyebrow">Vinyl Archive · Jacket Portfolio</span>

        <h1 id="hero-title" class="hero__title">
            내가 모은 음반,<br>내가 만든 자켓
        </h1>

        <p class="hero__subtitle">
            LP · CD 아카이브와 자켓 디자인 포트폴리오
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/collection.php" class="btn-amber">컬렉션 보기</a>
            <a href="/designs.php" class="btn-amber-outline">디자인 보기</a>
        </div>
    </div>
</section>

<!-- =========================================================================
     최근 추가 — Recent albums (4 cards)
     ========================================================================= -->
<section class="section" aria-labelledby="recent-albums-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Recently Added</span>
                <h2 id="recent-albums-title">최근 추가</h2>
            </div>
            <a href="/collection.php" class="btn-amber-outline">전체 컬렉션 &rarr;</a>
        </header>

        <?php if ($recent_albums): ?>
            <div class="sleeve-grid">
                <?php foreach ($recent_albums as $album): ?>
                    <a class="sleeve-card"
                       href="album.php?slug=<?= h($album['slug']) ?>"
                       aria-label="<?= h($album['artist']) ?> — <?= h($album['title']) ?>">
                        <div class="sleeve-card__cover">
                            <?php if (!empty($album['is_limited'])): ?>
                                <span class="badge-limited">한정반</span>
                            <?php endif; ?>
                            <img
                                src="<?= h($album['cover_path']) ?>"
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
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted-custom">아직 등록된 음반이 없습니다.</p>
        <?php endif; ?>
    </div>
</section>

<!-- =========================================================================
     최근 디자인 — Recent designs (3 cards)
     ========================================================================= -->
<section class="section bg-surface" aria-labelledby="recent-designs-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Latest Work</span>
                <h2 id="recent-designs-title">최근 디자인</h2>
            </div>
            <a href="/designs.php" class="btn-amber-outline">전체 디자인 &rarr;</a>
        </header>

        <?php if ($recent_designs): ?>
            <div class="sleeve-grid--3">
                <?php foreach ($recent_designs as $design): ?>
                    <a class="sleeve-card"
                       href="design.php?slug=<?= h($design['slug']) ?>"
                       aria-label="<?= h($design['title']) ?> 디자인 보기">
                        <div class="sleeve-card__cover">
                            <img
                                src="<?= h($design['thumb_path']) ?>"
                                alt="<?= h($design['title']) ?> 썸네일"
                                loading="lazy">
                        </div>
                        <div class="sleeve-card__body">
                            <h3 class="sleeve-card__title"><?= h($design['title']) ?></h3>
                            <?php if (!empty($design['tools'])): ?>
                                <p class="sleeve-card__artist"><?= h($design['tools']) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted-custom">아직 등록된 디자인이 없습니다.</p>
        <?php endif; ?>
    </div>
</section>

<!-- =========================================================================
     바이닐 문화 소개 — Static culture intro
     ========================================================================= -->
<section class="section" aria-labelledby="culture-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Why Vinyl</span>
                <h2 id="culture-title">바이닐 문화 소개</h2>
            </div>
        </header>

        <div class="sleeve-grid--3">
            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9737;</div>
                <h3 class="stats-card__title">Archive</h3>
                <h4 style="margin-bottom:var(--space-3);">아카이브</h4>
                <p>
                    좋아하는 음반을 한 장 한 장 기록합니다. 발매 연도, 포맷, 장르, 한정반 여부까지
                    수집한 컬렉션의 모든 정보를 체계적으로 정리합니다.
                </p>
            </article>

            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9998;</div>
                <h3 class="stats-card__title">Portfolio</h3>
                <h4 style="margin-bottom:var(--space-3);">포트폴리오</h4>
                <p>
                    직접 만든 자켓 디자인 작업물을 모아 둡니다. 의도와 작업 과정을 함께 남겨
                    아카이브와 창작이 자연스럽게 이어지도록 했습니다.
                </p>
            </article>

            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9835;</div>
                <h3 class="stats-card__title">Curation</h3>
                <h4 style="margin-bottom:var(--space-3);">큐레이션</h4>
                <p>
                    무드와 장르에 따라 음반을 다시 묶어 봅니다. 단순한 목록이 아니라 듣는
                    맥락을 함께 큐레이션하기 위한 공간입니다.
                </p>
            </article>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
