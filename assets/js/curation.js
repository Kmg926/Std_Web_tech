/* ============================================================
   SLEEVE — curation.js
   curation.html 전용. 무드/테마 큐레이션.
   - albums.json 로드
   - moods[] 기준으로 그룹핑 (없으면 genre 폴백)
   - 무드별 섹션(헤더 + 카드 그리드) 렌더
   - 새로고침마다 그룹 순서 + 각 그룹 내 앨범 셔플
   ============================================================ */
'use strict';

(async () => {
  const { escapeHTML } = window.SLEEVE;

  const albums = await SLEEVE.loadAlbums();

  const container = document.getElementById('curationGrid');
  const empty = document.getElementById('curationEmpty');
  const criterion = document.getElementById('curationCriterion');

  const MAX_GROUPS = 6;  // 표시할 최대 무드 그룹 수
  const PER_GROUP  = 3;  // 그룹당 최대 앨범 수

  /* Fisher-Yates 셔플 */
  function shuffle(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  }

  /* 무드 → 앨범[] 그룹 빌드
     - moods[] 있으면 각 mood에 앨범 등록
     - moods 없으면 genre를 대체 레이블로 사용
     - 그룹 순서 셔플 + 각 그룹 내 앨범도 셔플 */
  function buildMoodGroups(list) {
    const moodMap = new Map();

    list.forEach((album) => {
      const labels =
        Array.isArray(album.moods) && album.moods.length
          ? album.moods
          : album.genre ? [album.genre] : [];

      labels.forEach((label) => {
        if (!label) return;
        if (!moodMap.has(label)) moodMap.set(label, []);
        moodMap.get(label).push(album);
      });
    });

    return shuffle([...moodMap.entries()])
      .slice(0, MAX_GROUPS)
      .map(([mood, albms]) => ({
        mood,
        albums: shuffle(albms).slice(0, PER_GROUP),
      }));
  }

  /* 렌더 */
  function render(groups) {
    if (!container) return;

    if (!groups.length) {
      container.innerHTML = '';
      if (empty) empty.hidden = false;
      return;
    }

    if (empty) empty.hidden = true;

    // 상단 기준 안내
    if (criterion) {
      criterion.textContent =
        `기준: 무드 · ${groups.length}개 테마 · 새로고침마다 추천 앨범이 바뀝니다`;
    }

    container.innerHTML = groups
      .map(
        ({ mood, albums: albms }) => `
        <section class="curation-section">
          <h2 class="curation-section__label">${escapeHTML(mood)}</h2>
          <div class="curation-section__grid">
            ${albms
              .map((album) => {
                const cover = album.cover_path || 'assets/img/ui/placeholder.svg';
                return `
                <a class="curation-card" href="album.html?id=${escapeHTML(album.id)}"
                   aria-label="${escapeHTML(mood)} — ${escapeHTML(album.title)}, ${escapeHTML(album.artist)}">
                  <div class="card__cover">
                    <img data-src="${escapeHTML(cover)}" src="assets/img/ui/placeholder.svg"
                         alt="${escapeHTML(album.title)} 자켓" loading="lazy">
                  </div>
                  <div class="curation-card__body">
                    <p class="curation-card__title">${escapeHTML(album.title)}</p>
                    <p class="curation-card__artist">${escapeHTML(album.artist)}</p>
                  </div>
                </a>`;
              })
              .join('')}
          </div>
        </section>`
      )
      .join('');

    if (typeof window.initLazyLoad === 'function') window.initLazyLoad();
  }

  render(buildMoodGroups(albums));
})();
