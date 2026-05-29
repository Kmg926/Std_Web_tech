<?php
/**
 * SLEEVE — Designs gallery
 *
 * Lists all design works in a responsive 1/2/3 column grid.
 * Each card links to the design detail page.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo     = db();
$designs = get_designs($pdo);

$page_title = '자켓 디자인';
require_once __DIR__ . '/includes/header.php';
?>

<section class="designs-hero py-5">
  <div class="container-sleeve">
    <header class="designs-hero__head text-center text-lg-start">
      <h1 class="designs-hero__title">자켓 디자인</h1>
      <p class="designs-hero__sub mb-0">
        직접 제작한 자켓&middot;바이닐 디자인 포트폴리오
      </p>
    </header>
  </div>
</section>

<section class="designs-list pb-5">
  <div class="container-sleeve">

    <?php if (!$designs): ?>
      <div class="designs-empty text-center py-5">
        <p class="designs-empty__title mb-2">아직 등록된 디자인이 없습니다.</p>
        <p class="designs-empty__sub text-muted mb-0">
          새 작업물이 추가되면 이곳에 공개됩니다.
        </p>
      </div>
    <?php else: ?>

      <div class="sleeve-grid sleeve-grid--3">
        <?php foreach ($designs as $design): ?>
          <?php
            $tools_display = trim((string)($design['tools'] ?? ''));
            $created_ts    = !empty($design['created_at']) ? strtotime($design['created_at']) : false;
            $created_disp  = $created_ts ? date('Y.m', $created_ts) : '';
          ?>
          <a
            class="sleeve-card sleeve-card--design"
            href="/design.php?slug=<?= h($design['slug']) ?>"
            aria-label="<?= h($design['title']) ?> 자세히 보기">
            <div class="sleeve-card__cover position-relative">
              <img
                src="<?= h($design['thumb_path']) ?>"
                alt="<?= h($design['title']) ?> 썸네일"
                loading="lazy"
                decoding="async">
              <div class="card-overlay" aria-hidden="true">
                <span class="card-overlay__title"><?= h($design['title']) ?></span>
              </div>
            </div>
            <div class="sleeve-card__body">
              <h2 class="sleeve-card__title"><?= h($design['title']) ?></h2>
              <p class="sleeve-card__meta mb-0">
                <?php if ($tools_display !== ''): ?>
                  <span class="sleeve-card__tools"><?= h($tools_display) ?></span>
                <?php endif; ?>
                <?php if ($created_disp !== ''): ?>
                  <?php if ($tools_display !== ''): ?>
                    <span aria-hidden="true"> &middot; </span>
                  <?php endif; ?>
                  <time datetime="<?= h(date('Y-m', $created_ts)) ?>"><?= h($created_disp) ?></time>
                <?php endif; ?>
              </p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
