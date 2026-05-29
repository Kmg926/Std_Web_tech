-- =====================================================================
-- SLEEVE - seed data
-- Load AFTER schema.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- albums  (8 total: mix of Vinyl/CD, multiple genres, 2 limited)
-- ---------------------------------------------------------------------
INSERT INTO `albums`
  (`id`, `slug`, `title`, `artist`, `release_year`, `format`, `genre`, `cover_path`, `note`, `is_limited`)
VALUES
  (1, 'kind-of-blue',          'Kind of Blue',           'Miles Davis',        1959, 'Vinyl', 'Jazz',      'assets/img/covers/kind-of-blue.jpg',          'Modal jazz cornerstone. Pressed on 180g audiophile vinyl, sleeve still smells faintly of cardboard archive.', 0),
  (2, 'in-rainbows',            'In Rainbows',            'Radiohead',          2007, 'Vinyl', 'Rock',      'assets/img/covers/in-rainbows.jpg',           'The pay-what-you-want experiment. Side A still my favourite vinyl flip moment.',                              0),
  (3, 'selected-ambient-vol2',  'Selected Ambient Works Volume II', 'Aphex Twin', 1994, 'CD',  'Electronic','assets/img/covers/selected-ambient-vol2.jpg', 'Wordless tracks, numbered only. The CD edition has bonus material that the LP cannot fit.',                  0),
  (4, 'goldberg-variations',    'Goldberg Variations',    'Glenn Gould',        1981, 'CD',    'Classical', 'assets/img/covers/goldberg-variations.jpg',   'The 1981 digital re-recording. Humming and chair-creaks intact.',                                            0),
  (5, 'random-access-memories', 'Random Access Memories', 'Daft Punk',          2013, 'Vinyl', 'Electronic','assets/img/covers/random-access-memories.jpg','Double LP, gatefold sleeve. Limited mirrored cover edition.',                                                 1),
  (6, 'rumours',                'Rumours',                'Fleetwood Mac',      1977, 'Vinyl', 'Rock',      'assets/img/covers/rumours.jpg',               'Original 1977 US pressing. Warm midrange, slight surface noise but it adds character.',                       0),
  (7, 'blue',                   'Blue',                   'Joni Mitchell',      1971, 'Vinyl', 'Pop',       'assets/img/covers/blue.jpg',                  'Confessional songwriting blueprint. Reissue 2018, half-speed mastered.',                                      0),
  (8, 'discovery-jp-edition',   'Discovery (JP Limited)', 'Daft Punk',          2001, 'CD',    'Electronic','assets/img/covers/discovery-jp-edition.jpg',  'Japan-only limited slipcase edition with bonus tracks. OBI strip intact.',                                    1);

-- ---------------------------------------------------------------------
-- tracks (4-8 per album)
-- ---------------------------------------------------------------------
INSERT INTO `tracks` (`album_id`, `position`, `title`) VALUES
  -- 1. Kind of Blue
  (1, 1, 'So What'),
  (1, 2, 'Freddie Freeloader'),
  (1, 3, 'Blue in Green'),
  (1, 4, 'All Blues'),
  (1, 5, 'Flamenco Sketches'),

  -- 2. In Rainbows
  (2, 1, '15 Step'),
  (2, 2, 'Bodysnatchers'),
  (2, 3, 'Nude'),
  (2, 4, 'Weird Fishes / Arpeggi'),
  (2, 5, 'All I Need'),
  (2, 6, 'Faust Arp'),
  (2, 7, 'Reckoner'),
  (2, 8, 'House of Cards'),

  -- 3. Selected Ambient Works Vol. II
  (3, 1, 'Cliffs'),
  (3, 2, 'Radiator'),
  (3, 3, 'Rhubarb'),
  (3, 4, 'Hankie'),
  (3, 5, 'Grass'),
  (3, 6, 'Mould'),

  -- 4. Goldberg Variations
  (4, 1, 'Aria'),
  (4, 2, 'Variatio 1 a 1 Clav.'),
  (4, 3, 'Variatio 5 a 1 ovvero 2 Clav.'),
  (4, 4, 'Variatio 15 a 1 Clav. Canone alla Quinta. Andante'),
  (4, 5, 'Variatio 25 a 2 Clav. Adagio'),
  (4, 6, 'Aria da Capo'),

  -- 5. Random Access Memories
  (5, 1, 'Give Life Back to Music'),
  (5, 2, 'The Game of Love'),
  (5, 3, 'Giorgio by Moroder'),
  (5, 4, 'Within'),
  (5, 5, 'Instant Crush'),
  (5, 6, 'Lose Yourself to Dance'),
  (5, 7, 'Get Lucky'),
  (5, 8, 'Contact'),

  -- 6. Rumours
  (6, 1, 'Second Hand News'),
  (6, 2, 'Dreams'),
  (6, 3, 'Never Going Back Again'),
  (6, 4, 'Don''t Stop'),
  (6, 5, 'Go Your Own Way'),
  (6, 6, 'The Chain'),
  (6, 7, 'Gold Dust Woman'),

  -- 7. Blue
  (7, 1, 'All I Want'),
  (7, 2, 'My Old Man'),
  (7, 3, 'Little Green'),
  (7, 4, 'Carey'),
  (7, 5, 'Blue'),
  (7, 6, 'A Case of You'),
  (7, 7, 'The Last Time I Saw Richard'),

  -- 8. Discovery (JP Limited)
  (8, 1, 'One More Time'),
  (8, 2, 'Aerodynamic'),
  (8, 3, 'Digital Love'),
  (8, 4, 'Harder, Better, Faster, Stronger'),
  (8, 5, 'Crescendolls'),
  (8, 6, 'Something About Us'),
  (8, 7, 'Voyager'),
  (8, 8, 'Veridis Quo');

-- ---------------------------------------------------------------------
-- album_moods (2-3 per album)
-- moods: late-night, rainy-day, focus, energetic, melancholic, warm
-- ---------------------------------------------------------------------
INSERT INTO `album_moods` (`album_id`, `mood`) VALUES
  (1, 'late-night'),
  (1, 'focus'),
  (1, 'warm'),

  (2, 'rainy-day'),
  (2, 'melancholic'),

  (3, 'late-night'),
  (3, 'focus'),
  (3, 'rainy-day'),

  (4, 'focus'),
  (4, 'melancholic'),

  (5, 'energetic'),
  (5, 'warm'),
  (5, 'late-night'),

  (6, 'warm'),
  (6, 'melancholic'),

  (7, 'rainy-day'),
  (7, 'melancholic'),
  (7, 'late-night'),

  (8, 'energetic'),
  (8, 'warm');

-- ---------------------------------------------------------------------
-- designs (3 total)
-- ---------------------------------------------------------------------
INSERT INTO `designs`
  (`id`, `slug`, `title`, `tools`, `thumb_path`, `intent`, `process`)
VALUES
  (1, 'midnight-static',
      'Midnight Static',
      'Photoshop, Procreate, Risograph',
      'assets/img/designs/midnight-static.jpg',
      'A jacket concept for an imagined late-night ambient EP. The intent was to capture the visual texture of analog noise — the kind you hear when the needle hits a slightly worn groove.',
      'Started with a hand-drawn radial pattern in Procreate, then layered scanned grain in Photoshop. Final output mocked up as a two-color Risograph print (fluorescent pink + black) to keep the palette aggressive but warm.'),

  (2, 'coastline-blueprint',
      'Coastline Blueprint',
      'Illustrator, Figma',
      'assets/img/designs/coastline-blueprint.jpg',
      'Sleeve design for a folk record that does not exist yet. The brief I gave myself: imagine the album was pressed in 1973 and reissued today. The cover had to feel found, not designed.',
      'Sketched a topographic line study in Illustrator, then aged the type in Figma using subtle offset shadows. Tested 12 type pairings before landing on a paired display + monospaced caption system.'),

  (3, 'gridlocked',
      'Gridlocked',
      'Blender, Photoshop',
      'assets/img/designs/gridlocked.jpg',
      'Speculative electronic LP cover. Goal: explore how rigid grids can still feel emotional when paired with imperfect lighting.',
      '3D scene built in Blender — a single grid plane with volumetric lighting. Rendered three lighting passes and composited in Photoshop. The final cover hides a subtle artist sigil in the negative space.');

-- ---------------------------------------------------------------------
-- design_images (2-3 per design)
-- ---------------------------------------------------------------------
INSERT INTO `design_images` (`design_id`, `image_path`, `sort_order`) VALUES
  (1, 'assets/img/designs/midnight-static-01.jpg',     0),
  (1, 'assets/img/designs/midnight-static-02.jpg',     1),
  (1, 'assets/img/designs/midnight-static-detail.jpg', 2),

  (2, 'assets/img/designs/coastline-blueprint-01.jpg', 0),
  (2, 'assets/img/designs/coastline-blueprint-02.jpg', 1),

  (3, 'assets/img/designs/gridlocked-01.jpg',          0),
  (3, 'assets/img/designs/gridlocked-02.jpg',          1),
  (3, 'assets/img/designs/gridlocked-detail.jpg',      2);

-- ---------------------------------------------------------------------
-- guide_posts (4 total, 4 different categories)
-- ---------------------------------------------------------------------
INSERT INTO `guide_posts`
  (`slug`, `title`, `category`, `body`)
VALUES
  ('first-turntable-checklist',
   'Your First Turntable: A Checklist',
   'beginner',
   'Starting out, the easy mistake is to chase a beautiful turntable and forget about the phono stage, the cartridge alignment, and the speakers. Here is the order I recommend: 1) a belt-drive turntable from a known brand, 2) a built-in or external phono preamp, 3) actively powered bookshelf speakers. Skip suitcase players — they damage records.'),

  ('cleaning-and-storage',
   'Cleaning and Storage Without Special Equipment',
   'care',
   'You do not need a $400 ultrasonic cleaner to start. A carbon fibre brush before each play, anti-static inner sleeves, and vertical storage away from sunlight will do 90% of the work. Never stack records horizontally — the weight warps the bottom sleeves. If you must wet-clean, distilled water and a microfibre cloth, working with the grooves not across them.'),

  ('phono-cartridge-basics',
   'Phono Cartridge Basics: MM vs MC',
   'equipment',
   'Moving Magnet (MM) cartridges are the standard entry point — higher output, replaceable styli, forgiving of phono stages. Moving Coil (MC) cartridges have lower output and typically require a step-up transformer or a phono stage with an MC input, but they reveal more detail. For a first system, MM is almost always the right call. Upgrade the cartridge before you upgrade anything else.'),

  ('why-jackets-matter',
   'Why Album Jackets Still Matter',
   'culture',
   'A 12-inch jacket is the largest physical object most listeners ever hold from an artist. It frames the music before a single note plays. Streaming flattened cover art to a thumbnail, but the jacket survived because it does what a thumbnail cannot: it sets a tactile, scale-correct first impression. That is why I keep designing them, even for albums that will never be pressed.');
