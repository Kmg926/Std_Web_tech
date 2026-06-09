/* ============================================================
   SLEEVE — curation.js
   curation.html 전용. "오늘의 한 장" 무드/테마 큐레이션.
   - albums.json 로드
   - 각 앨범의 moods 배열로 그룹핑 (moods 없으면 genre 로 폴백)
   - 무드별 대표 앨범 1장을 골라 큐레이션 카드 렌더 (최대 6개)
   - 카드 클릭 → album.html?id=<id>
   - 빈 데이터 시 empty-state 폴백
   ============================================================ */
'use strict';

(async () => {
  const { escapeHTML } = window.SLEEVE;

  const albums = await SLEEVE.loadAlbums();

  const grid = document.getElementById('curationGrid');
  const empty = document.getElementById('curationEmpty');

  const MAX_CARDS = 6;

  /* Fisher-Yates 셔플 — 원본 배열 복사 후 무작위 순서 반환 */
  function shuffle(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  }

  /* --------------------------------------------------------
     1. 무드 → 대표 앨범 매핑
     앨범 배열을 먼저 셔플해 무드당 뽑히는 앨범이 매 로드마다 달라짐.
     moods 가 비어있는 앨범은 genre 를 대체 레이블로 사용.
     -------------------------------------------------------- */
  function buildCuration(list) {
    const moodMap = new Map(); // mood(label) → album

    shuffle(list).forEach((album) => {
      const labels =
        Array.isArray(album.moods) && album.moods.length
          ? album.moods
          : album.genre
          ? [album.genre]
          : [];

      labels.forEach((label) => {
        if (label == null || label === '') return;
        if (!moodMap.has(label)) {
          moodMap.set(label, album);
        }
      });
    });

    // 최대 MAX_CARDS 개 — 순서도 셔플해 카드 배치 자체도 변화
    return shuffle([...moodMap.entries()]).slice(0, MAX_CARDS);
  }

  /* --------------------------------------------------------
     2. 렌더
     -------------------------------------------------------- */
  function render(entries) {
    if (!grid) return;

    if (!entries.length) {
      grid.innerHTML = '';
      if (empty) empty.hidden = false;
      return;
    }

    if (empty) empty.hidden = true;

    grid.innerHTML = entries
      .map(([mood, album]) => {
        const cover = album.cover_path || 'assets/img/ui/placeholder.svg';
        return `
      <a class="curation-card" href="album.html?id=${escapeHTML(album.id)}" aria-label="${escapeHTML(mood)} — ${escapeHTML(album.title)}, ${escapeHTML(album.artist)}">
        <div class="card__cover">
          <img data-src="${escapeHTML(cover)}" src="assets/img/ui/placeholder.svg" alt="${escapeHTML(album.title)} 자켓" loading="lazy">
        </div>
        <div class="curation-card__body">
          <p class="curation-card__mood">${escapeHTML(mood)}</p>
          <p class="curation-card__desc">${escapeHTML(album.title)} &mdash; ${escapeHTML(album.artist)}</p>
        </div>
      </a>`;
      })
      .join('');

    // 동적 삽입된 img[data-src] lazy load 재초기화
    if (typeof window.initLazyLoad === 'function') window.initLazyLoad();
  }

  /* --------------------------------------------------------
     초기화
     -------------------------------------------------------- */
  render(buildCuration(albums));
})();
