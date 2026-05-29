<?php
/**
 * SLEEVE — Album detail page
 *
 * Renders a single album with its cover, tracklist, mood tags,
 * personal note, and a "related albums" rail (same genre).
 *
 * Query string:
 *   ?slug=<album-slug>
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = db();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!$slug) {
    redirect_404();
}

$album = get_album_by_slug($pdo, $slug);
if (!$album) {
    redirect_404();
}

$tracks  = get_tracks($pdo, $album['id']);
$moods   = get_album_moods($pdo, $album['id']);
$related = get_related_albums($pdo, $album['genre'], $album['id'], 4);

$page_title = $album['artist'] . ' — ' . $album['title'];
require_once __DIR__ . '/includes/header.php';
?>

<section class="album-detail py-5">
  <div class="container-sleeve">

    <nav class="sleeve-breadcrumb mb-4" aria-label="페이지 위치">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/index.php">홈</a></li>
        <li class="breadcrumb-item"><a href="/collection.php">컬렉션</a></li>
        <li class="breadcrumb-item active" aria-current="page">
          <?= h($album['artist']) ?> &mdash; <?= h($album['title']) ?>
        </li>
      </ol>
    </nav>

    <div class="row g-4 g-lg-5 align-items-start">

      <div class="col-12 col-lg-6">
        <figure class="album-detail__cover-wrap m-0">
          <div class="album-detail__cover position-relative">
            <img
              src="<?= h($album['cover_path']) ?>"
              alt="<?= h($album['artist'] . ' - ' . $album['title']) ?> 자켓"
              class="img-fluid w-100 d-block"
              loading="eager"
              decoding="async">
            <?php if (!empty($album['is_limited'])): ?>
              <span class="badge-limited" aria-label="한정반">한정반</span>
            <?php endif; ?>
          </div>
          <div class="vinyl-ring mt-4" aria-hidden="true"></div>
        </figure>
      </div>

      <div class="col-12 col-lg-6">
        <header class="album-detail__head">
          <p class="album-detail__artist mb-1"><?= h($album['artist']) ?></p>
          <h1 class="album-detail__title mb-3"><?= h($album['title']) ?></h1>

          <ul class="album-detail__meta list-inline mb-3">
            <?php if (!empty($album['release_year'])): ?>
              <li class="list-inline-item">
                <span class="album-detail__year"><?= h($album['release_year']) ?></span>
              </li>
            <?php endif; ?>
            <?php if (!empty($album['format'])): ?>
              <li class="list-inline-item">
                <span class="badge bg-dark"><?= h($album['format']) ?></span>
              </li>
            <?php endif; ?>
            <?php if (!empty($album['genre'])): ?>
              <li class="list-inline-item">
                <span class="album-detail__genre"><?= h($album['genre']) ?></span>
              </li>
            <?php endif; ?>
          </ul>

          <?php if (!empty($album['is_limited'])): ?>
            <p class="album-detail__limited mb-4">
              <span class="badge-limited badge-limited--inline">한정반</span>
            </p>
          <?php endif; ?>
        </header>

        <?php if ($tracks): ?>
          <section class="album-detail__section">
            <h2 class="album-detail__h2">트랙리스트</h2>
            <ol class="album-detail__tracks list-unstyled">
              <?php foreach ($tracks as $track): ?>
                <li class="album-detail__track">
                  <span class="album-detail__track-pos"><?= h($track['position']) ?>.</span>
                  <span class="album-detail__track-title"><?= h($track['title']) ?></span>
                </li>
              <?php endforeach; ?>
            </ol>
          </section>
        <?php endif; ?>

        <?php if ($moods): ?>
          <section class="album-detail__section">
            <h2 class="album-detail__h2">무드</h2>
            <ul class="mood-tags list-unstyled d-flex flex-wrap gap-2 mb-0">
              <?php foreach ($moods as $m): ?>
                <li><span class="mood-tag"><?= h($m) ?></span></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>

        <?php if (!empty($album['note'])): ?>
          <section class="album-detail__section">
            <h2 class="album-detail__h2">감상평</h2>
            <p class="album-detail__note"><?= nl2br(h($album['note'])) ?></p>
          </section>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php if ($related): ?>
  <section class="album-related py-5">
    <div class="container-sleeve">
      <header class="album-related__head mb-4">
        <h2 class="album-related__title">같은 장르</h2>
        <p class="album-related__sub text-muted mb-0">
          <?= h($album['genre']) ?> 장르의 다른 앨범
        </p>
      </header>

      <div class="sleeve-grid">
        <?php foreach ($related as $r): ?>
          <a class="sleeve-card" href="/album.php?slug=<?= h($r['slug']) ?>">
            <div class="sleeve-card__cover position-relative">
              <img
                src="<?= h($r['cover_path']) ?>"
                alt="<?= h($r['artist'] . ' - ' . $r['title']) ?> 자켓"
                loading="lazy"
                decoding="async">
              <?php if (!empty($r['is_limited'])): ?>
                <span class="badge-limited" aria-label="한정반">한정반</span>
              <?php endif; ?>
            </div>
            <div class="sleeve-card__body">
              <p class="sleeve-card__artist mb-1"><?= h($r['artist']) ?></p>
              <h3 class="sleeve-card__title"><?= h($r['title']) ?></h3>
              <p class="sleeve-card__meta mb-0">
                <?php if (!empty($r['release_year'])): ?>
                  <span><?= h($r['release_year']) ?></span>
                <?php endif; ?>
                <?php if (!empty($r['format'])): ?>
                  <span>&middot; <?= h($r['format']) ?></span>
                <?php endif; ?>
              </p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
