/* ============================================================
   SLEEVE — collection.js
   collection.html 전용.
   - albums.json 로드 → 카드 그리드 렌더
   - genre / format / year 필터 (그룹 내 단일 선택, 그룹 간 AND 교집합)
   - 클라이언트 사이드 필터링 (서버 쿼리 없음)
   ============================================================ */
'use strict';

(async () => {
  const { escapeHTML } = window.SLEEVE;

  const albums = await SLEEVE.loadAlbums();

  // 현재 활성 필터 상태. 'all' 은 해당 그룹 미적용.
  const activeFilters = {
    genre: 'all',
    format: 'all',
    year: 'all'
  };

  const grid = document.getElementById('albumGrid');
  const empty = document.getElementById('filterEmpty');
  const count = document.getElementById('albumCount');

  /* --------------------------------------------------------
     1. 카드 렌더
     -------------------------------------------------------- */
  function renderCards(list) {
    if (!grid) return;

    if (!list.length) {
      grid.innerHTML = '';
      if (empty) empty.hidden = false;
      if (count) count.textContent = '0';
      return;
    }

    if (empty) empty.hidden = true;
    if (count) count.textContent = String(list.length);

    grid.innerHTML = list
      .map(
        (album) => {
          const badges = [];
          if (album.is_signed)   badges.push('<span class="badge-signed">사인반</span>');
          if (album.is_bootleg)  badges.push('<span class="badge-bootleg">부틀렉</span>');
          const badgeGroup = badges.length ? `<div class="badge-group">${badges.join('')}</div>` : '';

          return `
      <a class="card" href="album.html?id=${escapeHTML(album.id)}" aria-label="${escapeHTML(album.artist)} — ${escapeHTML(album.title)}">
        <div class="card__cover">
          <img data-src="${escapeHTML(album.cover_path)}" src="assets/img/ui/placeholder.svg" alt="${escapeHTML(album.title)} 자켓" loading="lazy">
        </div>
        <div class="card__body">
          <p class="card__artist">${escapeHTML(album.artist)}</p>
          <h3 class="card__title">${escapeHTML(album.title)}</h3>
          ${badgeGroup}
          <div class="card__meta">
            <span>${escapeHTML(String(album.release_year))}</span>
            <span>${escapeHTML(album.format)}</span>
            <span>${escapeHTML(album.genre)}</span>
          </div>
        </div>
      </a>
    `;
        }
      )
      .join('');

    // 렌더 후 새로 삽입된 img[data-src] 에 대해 lazy load 재초기화
    if (typeof window.initLazyLoad === 'function') window.initLazyLoad();
  }

  /* --------------------------------------------------------
     2. 필터 — pill 생성 + 핸들러
     필터는 AND 조건: 여러 그룹이 동시에 활성이면 교집합.
     같은 그룹 안에서는 단일 선택(라디오처럼 동작).
     -------------------------------------------------------- */

  // 데이터에서 그룹별 고유 값 추출
  function uniqueValues(key) {
    return [...new Set(albums.map((a) => a[key]).filter((v) => v != null))];
  }

  // 연도는 숫자 내림차순, 나머지는 사전순
  function sortedValues(key) {
    const vals = uniqueValues(key);
    if (key === 'release_year') {
      return vals.map(Number).sort((a, b) => b - a);
    }
    return vals.sort((a, b) => String(a).localeCompare(String(b), 'en'));
  }

  /**
   * 하나의 필터 그룹(.filter-group[data-filter-group=...]) 안에
   * "전체" + 각 값에 대한 .filter-pill 버튼을 생성한다.
   * @param {string} groupName  genre | format | year
   * @param {string} dataKey    albums 객체의 실제 키 (year → release_year)
   */
  function buildFilterGroup(groupName, dataKey) {
    const container = document.querySelector(
      `[data-filter-group="${groupName}"]`
    );
    if (!container) return;

    const values = sortedValues(dataKey);

    // "전체" pill + 값 pill 들. 텍스트는 신뢰 데이터지만 일관성 위해 escape.
    const pills = [
      `<button type="button" class="filter-pill active" data-filter-value="all">전체</button>`,
      ...values.map(
        (v) =>
          `<button type="button" class="filter-pill" data-filter-value="${escapeHTML(
            String(v)
          )}">${escapeHTML(String(v))}</button>`
      )
    ];

    container.innerHTML = pills.join('');

    // 이벤트 위임: 컨테이너 한 곳에만 리스너를 둔다.
    container.addEventListener('click', (e) => {
      const pill = e.target.closest('.filter-pill');
      if (!pill || !container.contains(pill)) return;

      const value = pill.dataset.filterValue;
      activeFilters[groupName] = value;

      // 같은 그룹 내 active 토글 (단일 선택)
      container
        .querySelectorAll('.filter-pill')
        .forEach((p) => p.classList.toggle('active', p === pill));

      applyFilters();
    });
  }

  /* --------------------------------------------------------
     연도 구간 필터 (고정 버킷)
     -------------------------------------------------------- */
  const YEAR_BUCKETS = [
    { label: '~1990',   test: (y) => y <= 1990 },
    { label: '2010년대', test: (y) => y >= 2010 && y <= 2019 },
    { label: '2020년대', test: (y) => y >= 2020 },
  ];

  function buildYearFilter() {
    const container = document.querySelector('[data-filter-group="year"]');
    if (!container) return;

    const pills = [
      `<button type="button" class="filter-pill active" data-filter-value="all">전체</button>`,
      ...YEAR_BUCKETS.map(
        (b) => `<button type="button" class="filter-pill" data-filter-value="${escapeHTML(b.label)}">${escapeHTML(b.label)}</button>`
      )
    ];
    container.innerHTML = pills.join('');

    container.addEventListener('click', (ev) => {
      const pill = ev.target.closest('.filter-pill');
      if (!pill || !container.contains(pill)) return;
      activeFilters.year = pill.dataset.filterValue;
      container.querySelectorAll('.filter-pill').forEach((p) => p.classList.toggle('active', p === pill));
      applyFilters();
    });
  }

  /* --------------------------------------------------------
     3. 필터 적용 — 교집합(AND)으로 거른 뒤 재렌더
     -------------------------------------------------------- */
  function applyFilters() {
    const filtered = albums.filter((album) => {
      if (activeFilters.genre !== 'all' && album.genre !== activeFilters.genre) return false;
      if (activeFilters.format !== 'all' && album.format !== activeFilters.format) return false;
      if (activeFilters.year !== 'all') {
        const bucket = YEAR_BUCKETS.find((b) => b.label === activeFilters.year);
        if (!bucket || !bucket.test(Number(album.release_year))) return false;
      }
      return true;
    });

    renderCards(filtered);
  }

  /* --------------------------------------------------------
     초기화
     -------------------------------------------------------- */
  buildFilterGroup('genre', 'genre');
  buildFilterGroup('format', 'format');
  buildYearFilter();

  renderCards(albums);
})();
