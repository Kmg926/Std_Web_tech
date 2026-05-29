<?php
/**
 * SLEEVE - shared helper functions
 *
 * All DB access goes through PDO prepared statements.
 * Every function that builds SQL with optional filters whitelists the
 * filter keys before binding values — never interpolates user input.
 */

require_once __DIR__ . '/db.php';

// =====================================================================
// Output / utility
// =====================================================================

/**
 * Escape a string for safe HTML output.
 *
 * @param string|null $str
 * @return string
 */
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Render the 404 page and stop execution.
 *
 * @return void
 */
function redirect_404() {
    http_response_code(404);
    $page = __DIR__ . '/../404.php';
    if (file_exists($page)) {
        include $page;
    } else {
        echo 'Not Found';
    }
    exit;
}

/**
 * Coerce a value to a positive integer ID or 404.
 *
 * @param mixed $id
 * @return int
 */
function validate_id($id) {
    $n = (int)$id;
    if ($n > 0) {
        return $n;
    }
    redirect_404();
}

// =====================================================================
// Albums
// =====================================================================

/**
 * Fetch a list of albums with optional filters.
 *
 * Allowed filter keys: 'genre', 'format', 'release_year'.
 * Unknown keys are silently ignored (whitelist).
 *
 * @param PDO   $pdo
 * @param array $filters
 * @return array
 */
function get_albums(PDO $pdo, array $filters = []) {
    $where  = [];
    $params = [];

    if (!empty($filters['genre'])) {
        $where[]          = 'genre = :genre';
        $params[':genre'] = (string)$filters['genre'];
    }
    if (!empty($filters['format'])) {
        $fmt = (string)$filters['format'];
        // Whitelist ENUM values to be safe.
        if (in_array($fmt, ['Vinyl', 'CD'], true)) {
            $where[]           = 'format = :format';
            $params[':format'] = $fmt;
        }
    }
    if (!empty($filters['release_year'])) {
        $year = (int)$filters['release_year'];
        if ($year > 0) {
            $where[]                 = 'release_year = :release_year';
            $params[':release_year'] = $year;
        }
    }

    $sql = 'SELECT id, slug, title, artist, release_year, format, genre, cover_path, note, is_limited, created_at
            FROM albums';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY created_at DESC, id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Fetch a single album by its slug.
 *
 * @param PDO    $pdo
 * @param string $slug
 * @return array|null
 */
function get_album_by_slug(PDO $pdo, $slug) {
    $stmt = $pdo->prepare(
        'SELECT id, slug, title, artist, release_year, format, genre, cover_path, note, is_limited, created_at
         FROM albums
         WHERE slug = :slug
         LIMIT 1'
    );
    $stmt->execute([':slug' => (string)$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Tracks for an album, ordered by track position.
 *
 * @param PDO $pdo
 * @param int $album_id
 * @return array
 */
function get_tracks(PDO $pdo, $album_id) {
    $stmt = $pdo->prepare(
        'SELECT id, album_id, position, title
         FROM tracks
         WHERE album_id = :album_id
         ORDER BY position ASC, id ASC'
    );
    $stmt->execute([':album_id' => (int)$album_id]);
    return $stmt->fetchAll();
}

/**
 * Moods (tags) attached to an album.
 *
 * @param PDO $pdo
 * @param int $album_id
 * @return array  array of mood strings
 */
function get_album_moods(PDO $pdo, $album_id) {
    $stmt = $pdo->prepare(
        'SELECT mood
         FROM album_moods
         WHERE album_id = :album_id
         ORDER BY mood ASC'
    );
    $stmt->execute([':album_id' => (int)$album_id]);
    return array_column($stmt->fetchAll(), 'mood');
}

/**
 * Related albums in the same genre, excluding the current album.
 *
 * @param PDO    $pdo
 * @param string $genre
 * @param int    $current_id
 * @param int    $limit
 * @return array
 */
function get_related_albums(PDO $pdo, $genre, $current_id, $limit = 4) {
    $limit = max(1, min((int)$limit, 24));

    $sql = 'SELECT id, slug, title, artist, release_year, format, genre, cover_path, is_limited
            FROM albums
            WHERE genre = :genre AND id <> :current_id
            ORDER BY created_at DESC, id DESC
            LIMIT ' . $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':genre',      (string)$genre, PDO::PARAM_STR);
    $stmt->bindValue(':current_id', (int)$current_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// =====================================================================
// Designs
// =====================================================================

/**
 * Fetch all designs, newest first.
 *
 * @param PDO $pdo
 * @return array
 */
function get_designs(PDO $pdo) {
    $stmt = $pdo->query(
        'SELECT id, slug, title, tools, thumb_path, intent, process, created_at
         FROM designs
         ORDER BY created_at DESC, id DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Fetch a single design with its images attached as `images`.
 *
 * @param PDO    $pdo
 * @param string $slug
 * @return array|null
 */
function get_design_by_slug(PDO $pdo, $slug) {
    $stmt = $pdo->prepare(
        'SELECT id, slug, title, tools, thumb_path, intent, process, created_at
         FROM designs
         WHERE slug = :slug
         LIMIT 1'
    );
    $stmt->execute([':slug' => (string)$slug]);
    $design = $stmt->fetch();
    if (!$design) {
        return null;
    }
    $design['images'] = get_design_images($pdo, (int)$design['id']);
    return $design;
}

/**
 * Images for a design, ordered by sort_order.
 *
 * @param PDO $pdo
 * @param int $design_id
 * @return array
 */
function get_design_images(PDO $pdo, $design_id) {
    $stmt = $pdo->prepare(
        'SELECT id, design_id, image_path, sort_order
         FROM design_images
         WHERE design_id = :design_id
         ORDER BY sort_order ASC, id ASC'
    );
    $stmt->execute([':design_id' => (int)$design_id]);
    return $stmt->fetchAll();
}

// =====================================================================
// Curated / moods
// =====================================================================

/**
 * Return albums that have at least one mood, optionally filtered by a
 * specific mood. Each row includes `moods` (array of mood strings).
 *
 * Avoids N+1 by fetching moods once and joining in PHP.
 *
 * @param PDO         $pdo
 * @param string|null $mood
 * @return array
 */
function get_curated(PDO $pdo, $mood = null) {
    if ($mood) {
        // Restrict to albums tagged with the requested mood.
        $sql = 'SELECT a.id, a.slug, a.title, a.artist, a.release_year, a.format, a.genre,
                       a.cover_path, a.is_limited
                FROM albums a
                INNER JOIN album_moods am ON am.album_id = a.id
                WHERE am.mood = :mood
                ORDER BY a.created_at DESC, a.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':mood' => (string)$mood]);
    } else {
        // Any album with at least one mood tag.
        $sql = 'SELECT a.id, a.slug, a.title, a.artist, a.release_year, a.format, a.genre,
                       a.cover_path, a.is_limited
                FROM albums a
                INNER JOIN album_moods am ON am.album_id = a.id
                GROUP BY a.id, a.slug, a.title, a.artist, a.release_year, a.format, a.genre,
                         a.cover_path, a.is_limited, a.created_at
                ORDER BY a.created_at DESC, a.id DESC';
        $stmt = $pdo->query($sql);
    }
    $albums = $stmt->fetchAll();
    if (!$albums) {
        return [];
    }

    // Bulk-fetch moods for all album IDs in one round trip.
    $ids = array_map(static function ($a) { return (int)$a['id']; }, $albums);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $moodsStmt = $pdo->prepare(
        'SELECT album_id, mood
         FROM album_moods
         WHERE album_id IN (' . $placeholders . ')
         ORDER BY mood ASC'
    );
    $moodsStmt->execute($ids);

    $byAlbum = [];
    foreach ($moodsStmt->fetchAll() as $row) {
        $byAlbum[(int)$row['album_id']][] = $row['mood'];
    }

    foreach ($albums as &$album) {
        $album['moods'] = $byAlbum[(int)$album['id']] ?? [];
    }
    unset($album);

    return $albums;
}

// =====================================================================
// Guide posts
// =====================================================================

/**
 * Fetch guide posts, optionally filtered by category.
 *
 * @param PDO         $pdo
 * @param string|null $category
 * @return array
 */
function get_guide_posts(PDO $pdo, $category = null) {
    if ($category) {
        $stmt = $pdo->prepare(
            'SELECT id, slug, title, category, body, created_at
             FROM guide_posts
             WHERE category = :category
             ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([':category' => (string)$category]);
    } else {
        $stmt = $pdo->query(
            'SELECT id, slug, title, category, body, created_at
             FROM guide_posts
             ORDER BY created_at DESC, id DESC'
        );
    }
    return $stmt->fetchAll();
}

// =====================================================================
// Stats
// =====================================================================

/**
 * Aggregate statistics about the archive.
 *
 * @param PDO $pdo
 * @return array{
 *   total_albums:int,
 *   total_vinyl:int,
 *   total_cd:int,
 *   genres:array<string,int>,
 *   years:array<int,int>,
 *   formats:array<string,int>
 * }
 */
function get_stats(PDO $pdo) {
    $stats = [
        'total_albums' => 0,
        'total_vinyl'  => 0,
        'total_cd'     => 0,
        'genres'       => [],
        'years'        => [],
        'formats'      => [],
    ];

    // Formats + totals in one pass.
    $rows = $pdo->query(
        'SELECT format, COUNT(*) AS c
         FROM albums
         GROUP BY format'
    )->fetchAll();
    foreach ($rows as $r) {
        $fmt = (string)$r['format'];
        $c   = (int)$r['c'];
        $stats['formats'][$fmt] = $c;
        $stats['total_albums']  += $c;
        if ($fmt === 'Vinyl') {
            $stats['total_vinyl'] = $c;
        } elseif ($fmt === 'CD') {
            $stats['total_cd'] = $c;
        }
    }

    // Genre histogram.
    $rows = $pdo->query(
        "SELECT COALESCE(genre, 'Unknown') AS genre, COUNT(*) AS c
         FROM albums
         GROUP BY genre
         ORDER BY c DESC, genre ASC"
    )->fetchAll();
    foreach ($rows as $r) {
        $stats['genres'][(string)$r['genre']] = (int)$r['c'];
    }

    // Year histogram (skip NULL years).
    $rows = $pdo->query(
        'SELECT release_year, COUNT(*) AS c
         FROM albums
         WHERE release_year IS NOT NULL
         GROUP BY release_year
         ORDER BY release_year ASC'
    )->fetchAll();
    foreach ($rows as $r) {
        $stats['years'][(int)$r['release_year']] = (int)$r['c'];
    }

    return $stats;
}
