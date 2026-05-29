<?php
/**
 * SLEEVE — About (about.php)
 *
 * Static "introduction" page: archive purpose, operator profile, core values,
 * and a small live stats block fed by get_stats().
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo        = db();
$page_title = '소개';
$stats      = get_stats($pdo);

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     About Hero
     ========================================================================= -->
<section class="hero" aria-labelledby="about-hero-title">
    <div class="hero__inner">
        <span class="hero__eyebrow">About</span>
        <h1 id="about-hero-title" class="hero__title">SLEEVE</h1>
        <p class="hero__subtitle">
            바이닐 아카이브 + 자켓 디자인 포트폴리오<br>
            모은 음반과 만든 자켓이 한곳에서 만나는 공간입니다.
        </p>
    </div>
</section>

<!-- =========================================================================
     아카이브 소개 — 2-col (text + vinyl motif)
     ========================================================================= -->
<section class="section" aria-labelledby="archive-intro-title">
    <div class="container-sleeve">
        <div class="album-detail">
            <div class="album-detail__meta">
                <span class="section-title__eyebrow">Introduction</span>
                <h2 id="archive-intro-title">아카이브 소개</h2>
                <hr class="divider-amber" aria-hidden="true">

                <p>
                    SLEEVE는 한 컬렉터가 모은 LP와 CD, 그리고 그 음반에 대한 라이너 노트를
                    기록하기 위한 개인 아카이브입니다. 발매 연도, 포맷, 장르, 한정반 여부 같은
                    데이터를 정리해 두면, 시간이 흐른 뒤에도 그날의 감상이 또렷하게 남습니다.
                </p>
                <p>
                    동시에 SLEEVE는 자켓 디자인 포트폴리오이기도 합니다. 좋아하는 음반을
                    듣는 일과 그것을 위한 자켓을 직접 그려 보는 일이 따로 떨어져 있지 않다는
                    것을, 이 사이트를 통해 보여드리고 싶습니다.
                </p>
                <p>
                    수집 · 창작 · 공유 — 세 가지 축을 중심으로 컬렉션, 디자인, 큐레이션,
                    가이드 페이지가 서로 연결되도록 구성했습니다.
                </p>
            </div>

            <div class="album-detail__cover" style="background:transparent; box-shadow:none; display:flex; align-items:center; justify-content:center;">
                <div class="vinyl-ring" aria-hidden="true"></div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     운영자 소개
     ========================================================================= -->
<section class="section bg-surface" aria-labelledby="operator-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Operator</span>
                <h2 id="operator-title">운영자 소개</h2>
            </div>
        </header>

        <div class="stats-card" style="max-width:760px;">
            <p>
                음악과 그래픽 디자인을 좋아하는 컬렉터입니다. 좋아하는 음반을 기록하고
                직접 만든 자켓 디자인을 공유하기 위해 이 아카이브를 만들었습니다.
            </p>
            <p>
                자주 듣는 장르는 록, 재즈, 일렉트로닉입니다. 디자인 작업에서는 활자와 색
                두 가지에 가장 많은 시간을 씁니다.
            </p>
        </div>
    </div>
</section>

<!-- =========================================================================
     핵심 가치 — Core values (3 cards)
     ========================================================================= -->
<section class="section" aria-labelledby="values-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Core Values</span>
                <h2 id="values-title">핵심 가치</h2>
            </div>
        </header>

        <div class="sleeve-grid--3">
            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9737;</div>
                <h3>수집 — Archive</h3>
                <p>
                    음반은 그 자체로 시간의 기록입니다. 발매 정보와 감상을 함께 남겨
                    컬렉션이 단순한 목록이 아니라 개인적인 연대기로 남도록 합니다.
                </p>
            </article>

            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9998;</div>
                <h3>창작 — Portfolio</h3>
                <p>
                    좋아하는 음악에 어울리는 자켓을 상상하고 직접 디자인합니다. 의도와
                    과정까지 함께 기록해 결과물만 보이는 포트폴리오가 되지 않도록 합니다.
                </p>
            </article>

            <article class="stats-card">
                <div aria-hidden="true" style="font-size:2.25rem; color:var(--amber); margin-bottom:var(--space-3);">&#9835;</div>
                <h3>공유 — Curation</h3>
                <p>
                    무드와 장르를 기준으로 음반을 다시 묶어 보며, 듣는 사람의 맥락에 맞는
                    추천을 큐레이션 페이지에서 함께 나눕니다.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- =========================================================================
     컬렉션 현황 — Live stats from get_stats()
     ========================================================================= -->
<section class="section bg-surface" aria-labelledby="collection-stats-title">
    <div class="container-sleeve">
        <header class="section-title">
            <div>
                <span class="section-title__eyebrow">Collection Snapshot</span>
                <h2 id="collection-stats-title">컬렉션 현황</h2>
            </div>
            <a href="/stats.php" class="btn-amber-outline">자세한 통계 &rarr;</a>
        </header>

        <div class="stats-grid">
            <article class="stats-card" aria-labelledby="stat-total">
                <p id="stat-total" class="stats-card__title">전체 음반</p>
                <span class="stat-number"><?= h(number_format((int)($stats['total_albums'] ?? 0))) ?></span>
                <span class="stat-label">Total Releases</span>
            </article>

            <article class="stats-card" aria-labelledby="stat-vinyl">
                <p id="stat-vinyl" class="stats-card__title">바이닐</p>
                <span class="stat-number"><?= h(number_format((int)($stats['total_vinyl'] ?? 0))) ?></span>
                <span class="stat-label">LP / Vinyl</span>
            </article>

            <article class="stats-card" aria-labelledby="stat-cd">
                <p id="stat-cd" class="stats-card__title">CD</p>
                <span class="stat-number"><?= h(number_format((int)($stats['total_cd'] ?? 0))) ?></span>
                <span class="stat-label">Compact Disc</span>
            </article>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
