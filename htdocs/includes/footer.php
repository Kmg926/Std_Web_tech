<?php
/**
 * SLEEVE — Shared Footer
 *
 * Closes <main>, renders the global footer, then loads JS bundles
 * and closes <body>/<html>.
 *
 * Relies on h() being defined in header.php (or globally).
 */

if (!function_exists('h')) {
    function h($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$nav_links = [
    ['/collection.php', '컬렉션'],
    ['/designs.php',    '디자인'],
    ['/curation.php',   '큐레이션'],
    ['/guide.php',      '가이드'],
    ['/stats.php',      '통계'],
    ['/about.php',      '소개'],
];

$current_year = date('Y');
?>
</main>

<footer class="footer-sleeve" role="contentinfo">
    <div class="container-sleeve">
        <div class="footer-sleeve__grid">

            <div class="footer-sleeve__col">
                <a href="/index.php" class="footer-sleeve__brand d-inline-block" aria-label="SLEEVE 홈">SLEEVE</a>
                <p class="footer-sleeve__tagline">
                    바이닐 아카이브 + 자켓 포트폴리오
                </p>
                <div class="footer-sleeve__social" aria-label="소셜 링크">
                    <a href="#" aria-label="Instagram" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                        </svg>
                    </a>
                    <a href="#" aria-label="GitHub" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="footer-sleeve__col">
                <h2 class="footer-sleeve__heading">Navigation</h2>
                <nav class="footer-sleeve__links" aria-label="푸터 메뉴">
                    <?php foreach ($nav_links as [$href, $label]): ?>
                        <a href="<?= h($href) ?>"><?= h($label) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="footer-sleeve__col">
                <h2 class="footer-sleeve__heading">About SLEEVE</h2>
                <p class="footer-sleeve__tagline">
                    수집한 바이닐의 기록과 직접 디자인한 자켓 작업물을 한곳에 모은 개인 아카이브입니다.
                </p>
            </div>

        </div>

        <div class="footer-sleeve__bottom">
            &copy; <?= h($current_year) ?> SLEEVE. All rights reserved.
        </div>
    </div>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="/assets/js/main.js"></script>

</body>
</html>
