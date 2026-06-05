/* ============================================================
   SLEEVE — album.js
   album.html 전용. URL ?id=slug 로 앨범 상세를 렌더.
   - albums.json 에서 id 매칭. 없거나 잘못된 id → 404.html
   - 커버 / 메타 / 트랙리스트 / 무드 / 감상평 / 관련 음반
   - 모든 동적 텍스트는 SLEEVE.escapeHTML 처리
   ============================================================ */
'use strict';

(async () => {
  const { escapeHTML } = window.SLEEVE;

  const params = new URLSearchParams(location.search);
  const id = params.get('id');
  if (!id) {
    location.href = '404.html';
    return;
  }

  const albums = await SLEEVE.loadAlbums();
  const album = albums.find((a) => a.id === id);
  if (!album) {
    location.href = '404.html';
    return;
  }

  // 문서 제목 갱신
  document.title = `${album.artist} — ${album.title} | SLEEVE`;

  const root = document.getElementById('album-detail');
  if (!root) return;

  /* --------------------------------------------------------
     부분 렌더 헬퍼
     -------------------------------------------------------- */

  // 감상평: escape 후 줄바꿈을 <br> 로. (escape 가 먼저라 안전)
  function renderNote(note) {
    if (!note) return '';
    const safe = escapeHTML(note).replace(/\r?\n/g, '<br>');
    return `
      <section class="album__note">
        <h2 class="album__section-title">감상평</h2>
        <p>${safe}</p>
      </section>`;
  }

  // 트랙리스트: 순서가 있으므로 <ol>. position 은 시각 표기용.
  function renderTracks(tracks) {
    if (!Array.isArray(tracks) || !tracks.length) return '';
    const items = tracks
      .map(
        (t) => `
        <li class="tracklist__item">
          <span class="tracklist__pos">${escapeHTML(String(t.position))}</span>
          <span class="tracklist__title">${escapeHTML(t.title)}</span>
        </li>`
      )
      .join('');
    return `
      <section class="album__tracks">
        <h2 class="album__section-title">트랙리스트</h2>
        <ol class="tracklist">${items}</ol>
      </section>`;
  }

  // 무드 태그
  function renderMoods(moods) {
    if (!Array.isArray(moods) || !moods.length) return '';
    const tags = moods
      .map((m) => `<span class="tag">${escapeHTML(m)}</span>`)
      .join('');
    return `<div class="album__moods">${tags}</div>`;
  }

  // 관련 음반: 같은 genre, 현재 앨범 제외, 최대 4개
  function renderRelated() {
    const related = albums
      .filter((a) => a.genre === album.genre && a.id !== album.id)
      .slice(0, 4);
    if (!related.length) return '';

    const cards = related
      .map(
        (a) => `
        <a class="card" href="album.html?id=${escapeHTML(a.id)}" aria-label="${escapeHTML(a.artist)} — ${escapeHTML(a.title)}">
          <div class="card__cover">
            ${a.is_limited ? '<span class="badge-limited">한정반</span>' : ''}
            <img data-src="${escapeHTML(a.cover_path)}" src="assets/img/ui/placeholder.svg" alt="${escapeHTML(a.title)} 자켓" loading="lazy">
          </div>
          <div class="card__body">
            <p class="card__artist">${escapeHTML(a.artist)}</p>
            <h3 class="card__title">${escapeHTML(a.title)}</h3>
          </div>
        </a>`
      )
      .join('');

    return `
      <section class="album__related">
        <h2 class="album__section-title">관련 음반</h2>
        <div class="card-grid">${cards}</div>
      </section>`;
  }

  /* --------------------------------------------------------
     전체 렌더
     onerror: 깨진 커버는 placeholder 로 폴백 (인라인 핸들러는
     마크업이 신뢰 데이터라 안전, 외부 입력 미포함).
     -------------------------------------------------------- */
  root.innerHTML = `
    <article class="album">
      <div class="album__cover">
        ${album.is_limited ? '<span class="badge-limited">한정반</span>' : ''}
        <img src="${escapeHTML(album.cover_path)}"
             alt="${escapeHTML(album.title)} 자켓"
             onerror="this.onerror=null;this.src='assets/img/ui/placeholder.svg';">
      </div>

      <div class="album__info">
        <p class="album__artist">${escapeHTML(album.artist)}</p>
        <h1 class="album__title">${escapeHTML(album.title)}</h1>

        <ul class="album__meta">
          <li><span class="album__meta-label">연도</span> ${escapeHTML(String(album.release_year))}</li>
          <li><span class="album__meta-label">포맷</span> ${escapeHTML(album.format)}</li>
          <li><span class="album__meta-label">장르</span> ${escapeHTML(album.genre)}</li>
        </ul>

        ${renderMoods(album.moods)}
      </div>
    </article>

    ${renderTracks(album.tracks)}
    ${renderNote(album.note)}
    ${renderRelated()}
  `;

  // 관련 음반 카드의 data-src lazy load 재초기화
  if (typeof window.initLazyLoad === 'function') window.initLazyLoad();
})();
