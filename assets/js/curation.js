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

  /* --------------------------------------------------------
     1. 무드 → 대표 앨범 매핑
     같은 무드가 여러 앨범에 있으면 첫 번째 앨범을 대표로 사용.
     moods 가 비어있는 앨범은 genre 를 대체 레이블로 사용.
     Map 으로 삽입 순서를 보존(앨범 등장 순서대로 무드 노출).
     -------------------------------------------------------- */
  function buildCuration(list) {
    const moodMap = new Map(); // mood(label) → album

    list.forEach((album) => {
      // moods 가 배열이고 값이 있으면 그대로, 없으면 genre 단일 레이블로 폴백
      const labels =
        Array.isArray(album.moods) && album.moods.length
          ? album.moods
          : album.genre
          ? [album.genre]
          : [];

      labels.forEach((label) => {
        if (label == null || label === '') return;
        // 같은 무드는 첫 번째 앨범만 등록 (이미 있으면 건너뜀)
        if (!moodMap.has(label)) {
          moodMap.set(label, album);
        }
      });
    });

    // 최대 MAX_CARDS 개로 제한
    return [...moodMap.entries()].slice(0, MAX_CARDS);
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
          ${album.is_limited ? '<span class="badge-limited">한정반</span>' : ''}
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
