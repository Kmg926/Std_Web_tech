<?php
/**
 * SLEEVE — Design detail page
 *
 * Shows a single design with hero image, intent, process,
 * tools, creation date, and a gallery of all attached images.
 *
 * Query string:
 *   ?slug=<design-slug>
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!$slug) {
    redirect_404();
}

$design = get_design_by_slug($pdo, $slug);
if (!$design) {
    redirect_404();
}

// get_design_by_slug() already attaches `images`, but the spec mandates
// an explicit call — keep them aligned in case the upstream changes.
$images = get_design_images($pdo, $design['id']);

// Resolve hero image: first gallery image, else thumb fallback.
$hero_path = '';
if (!empty($images)) {
    $hero_path = $images[0]['image_path'];
} elseif (!empty($design['thumb_path'])) {
    $hero_path = $design['thumb_path'];
}

// Tools come in as a single string (e.g. "Photoshop, Illustrator").
// Normalize into an array of trimmed, non-empty pills.
$tools_list = [];
if (!empty($design['tools'])) {
    foreach (explode(',', (string)$design['tools']) as $tool) {
        $tool = trim($tool);
        if ($tool !== '') {
            $tools_list[] = $tool;
        }
    }
}

$created_ts   = !empty($design['created_at']) ? strtotime($design['created_at']) : false;
$created_disp = $created_ts ? date('Y년 m월', $created_ts) : '';

$page_title = $design['title'];
require_once __DIR__ . '/includes/header.php';
?>

<section class="design-detail">

  <div class="container-sleeve pt-4">
    <nav class="sleeve-breadcrumb mb-4" aria-label="페이지 위치">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/index.php">홈</a></li>
        <li class="breadcrumb-item"><a href="/designs.php">디자인</a></li>
        <li class="breadcrumb-item active" aria-current="page">
          <?= h($design['title']) ?>
        </li>
      </ol>
    </nav>
  </div>

  <?php if ($hero_path !== ''): ?>
    <figure class="design-hero m-0">
      <img
        src="<?= h($hero_path) ?>"
        alt="<?= h($design['title']) ?> 대표 이미지"
        class="design-hero__img"
        loading="eager"
        decoding="async">
      <figcaption class="design-hero__overlay">
        <h1 class="design-hero__title"><?= h($design['title']) ?></h1>
      </figcaption>
    </figure>
  <?php else: ?>
    <div class="container-sleeve">
      <h1 class="design-detail__title"><?= h($design['title']) ?></h1>
    </div>
  <?php endif; ?>

  <div class="container-sleeve py-5">

    <div class="row g-4 g-lg-5">

      <div class="col-12 col-lg-8">
        <?php if (!empty($design['intent'])): ?>
          <section class="design-detail__section">
            <h2 class="design-detail__h2">제작 의도</h2>
            <p class="design-detail__body"><?= nl2br(h($design['intent'])) ?></p>
          </section>
        <?php endif; ?>

        <?php if (!empty($design['process'])): ?>
          <section class="design-detail__section">
            <h2 class="design-detail__h2">제작 과정</h2>
            <p class="design-detail__body"><?= nl2br(h($design['process'])) ?></p>
          </section>
        <?php endif; ?>
      </div>

      <aside class="col-12 col-lg-4">
        <div class="design-detail__sidebar">

          <?php if ($tools_list): ?>
            <section class="design-detail__section">
              <h2 class="design-detail__h2">사용 도구</h2>
              <ul class="mood-tags list-unstyled d-flex flex-wrap gap-2 mb-0">
                <?php foreach ($tools_list as $tool): ?>
                  <li><span class="mood-tag"><?= h($tool) ?></span></li>
                <?php endforeach; ?>
              </ul>
            </section>
          <?php endif; ?>

          <?php if ($created_disp !== ''): ?>
            <section class="design-detail__section">
              <h2 class="design-detail__h2">제작일</h2>
              <p class="design-detail__date mb-0">
                <time datetime="<?= h(date('Y-m', $created_ts)) ?>"><?= h($created_disp) ?></time>
              </p>
            </section>
          <?php endif; ?>

        </div>
      </aside>

    </div>

    <?php if ($images): ?>
      <section class="design-gallery mt-5">
        <h2 class="design-detail__h2">이미지 갤러리</h2>

        <?php if (count($images) === 1): ?>
          <?php $img = $images[0]; ?>
          <figure class="design-gallery__single m-0">
            <img
              src="<?= h($img['image_path']) ?>"
              alt="<?= h($design['title']) ?> 이미지"
              class="img-fluid w-100 d-block"
              loading="lazy"
              decoding="async">
          </figure>
        <?php else: ?>
          <div class="sleeve-grid sleeve-grid--3 design-gallery__grid">
            <?php foreach ($images as $i => $img): ?>
              <figure class="design-gallery__item m-0">
                <img
                  src="<?= h($img['image_path']) ?>"
                  alt="<?= h($design['title']) ?> 이미지 <?= h($i + 1) ?>"
                  class="img-fluid w-100 d-block"
                  loading="lazy"
                  decoding="async">
              </figure>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <p class="design-detail__back mt-5">
      <a href="/designs.php" class="design-detail__back-link">&larr; 디자인 목록으로</a>
    </p>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
