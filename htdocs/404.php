<?php
/**
 * SLEEVE — 404 Not Found
 *
 * Custom error page rendered for missing routes. The db/functions includes
 * are guarded because this page can be invoked very early (e.g. before the
 * project is fully wired up) — the page must still render in that case.
 */

http_response_code(404);

$page_title = '페이지를 찾을 수 없습니다';

if (file_exists(__DIR__ . '/includes/db.php')) {
    require_once __DIR__ . '/includes/db.php';
}
if (file_exists(__DIR__ . '/includes/functions.php')) {
    require_once __DIR__ . '/includes/functions.php';
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     404 — centered hero with vinyl motif
     ========================================================================= -->
<section class="hero" aria-labelledby="notfound-title">
    <div class="hero__inner" style="display:flex; flex-direction:column; align-items:center;">

        <span class="hero__eyebrow">Error 404</span>

        <p aria-hidden="true"
           style="
                font-family: var(--font-display);
                font-weight: 700;
                font-size: clamp(5rem, 14vw + 1rem, 10rem);
                line-height: 1;
                color: var(--amber);
                margin: 0 0 var(--space-5);
                letter-spacing: -0.02em;
           ">404</p>

        <h1 id="notfound-title" class="hero__title" style="font-size: clamp(1.75rem, 2vw + 1rem, 2.5rem);">
            페이지를 찾을 수 없습니다
        </h1>

        <p class="hero__subtitle">
            요청하신 페이지가 존재하지 않거나 이동되었습니다.
        </p>

        <hr class="divider-amber divider-amber--center" aria-hidden="true">

        <div style="
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-3);
            justify-content: center;
            margin-top: var(--space-5);
        ">
            <a href="/index.php" class="btn-amber">&larr; 홈으로 돌아가기</a>
            <a href="/collection.php" class="btn-amber-outline">컬렉션 보기</a>
        </div>

        <div style="margin-top: var(--space-8); display:flex; justify-content:center;">
            <div class="vinyl-ring" aria-hidden="true"></div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
