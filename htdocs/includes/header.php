<?php
/**
 * SLEEVE — Shared Header
 *
 * Outputs the HTML <head>, opens <body>, and renders the global navbar.
 * Expects:
 *   - $page_title (string, optional) — page-specific title.
 *
 * A helper h() is defined here as a fallback if the project hasn't already
 * provided one. It is safe to include this file even if h() exists elsewhere.
 */

if (!function_exists('h')) {
    /**
     * HTML-escape a string for safe output.
     *
     * @param mixed $value
     * @return string
     */
    function h($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Resolve current script for active-link detection.
$current_script = basename($_SERVER['PHP_SELF'] ?? '');

/**
 * Build a nav link with active state.
 *
 * @param string $href   Relative href, e.g. "/collection.php"
 * @param string $label  Display label
 * @param string $current Current script filename
 * @return string
 */
function sleeve_nav_link(string $href, string $label, string $current): string {
    $target = basename(parse_url($href, PHP_URL_PATH) ?? '');
    $is_active = ($target !== '' && $target === $current);
    $classes = 'nav-link' . ($is_active ? ' active' : '');
    $aria = $is_active ? ' aria-current="page"' : '';

    return sprintf(
        '<a class="%s" href="%s"%s>%s</a>',
        h($classes),
        h($href),
        $aria,
        h($label)
    );
}

$nav_items = [
    ['/collection.php', '컬렉션'],
    ['/designs.php',    '디자인'],
    ['/curation.php',   '큐레이션'],
    ['/guide.php',      '가이드'],
    ['/stats.php',      '통계'],
    ['/about.php',      '소개'],
];

$title = $page_title ?? 'SLEEVE';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1C1714">
    <meta name="description" content="SLEEVE — 바이닐 아카이브와 자켓 디자인 포트폴리오">

    <title><?= h($title) ?> &mdash; SLEEVE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora:wght@400;600&family=Inter:wght@400;500&family=Noto+Sans+KR:wght@400&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-sleeve" aria-label="주 메뉴">
        <div class="container-sleeve d-flex align-items-center justify-content-between w-100">
            <a class="navbar-brand" href="/index.php" aria-label="SLEEVE 홈">SLEEVE</a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sleeveNav"
                aria-controls="sleeveNav"
                aria-expanded="false"
                aria-label="메뉴 토글">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="sleeveNav">
                <div class="navbar-nav align-items-lg-center gap-lg-1">
                    <?php foreach ($nav_items as [$href, $label]): ?>
                        <?= sleeve_nav_link($href, $label, $current_script) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main id="main-content">
