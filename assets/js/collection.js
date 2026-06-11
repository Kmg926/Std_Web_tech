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
    genreCategory: 'all', // 대분류 (R&B / 록 / 힙합 / 팝 ...)
    genreSub: 'all',      // 대분류 안의 세부 장르
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
     장르 대분류 필터
     세부 장르(예: 한국 힙합, 산업 힙합)를 4개 대분류(R&B / 록 / 힙합 / 팝)로
     묶어 1차로 보여주고, 대분류를 클릭하면 그 안의 세부 장르 pill 행이
     나타나 추가로 좁힐 수 있게 한다. 세부 장르가 1개뿐인 대분류는
     세부 행을 표시하지 않는다.
     -------------------------------------------------------- */
  const GENRE_CATEGORY_ORDER = ['R&B', '록', '힙합', '팝'];

  function genreCategoryOf(genre) {
    if (genre.includes('힙합')) return '힙합';
    if (genre.includes('록')) return '록';
    if (genre.includes('R&B')) return 'R&B';
    if (genre.includes('팝')) return '팝';
    return genre;
  }

  function buildGenreGroups() {
    const groups = new Map();
    sortedValues('genre').forEach((g) => {
      const cat = genreCategoryOf(g);
      if (!groups.has(cat)) groups.set(cat, []);
      groups.get(cat).push(g);
    });
    return groups;
  }

  function buildGenreFilter() {
    const container = document.querySelector('[data-filter-group="genre"]');
    const subContainer = document.querySelector('[data-filter-group="genre-sub"]');
    const subGroup = document.getElementById('genreSubGroup');
    if (!container) return;

    const genreGroups = buildGenreGroups();

    // 지정된 순서(R&B/록/힙합/팝) 우선, 그 외 카테고리는 사전순으로 뒤에 추가
    const categories = [
      ...GENRE_CATEGORY_ORDER.filter((c) => genreGroups.has(c)),
      ...[...genreGroups.keys()]
        .filter((c) => !GENRE_CATEGORY_ORDER.includes(c))
        .sort((a, b) => a.localeCompare(b, 'en'))
    ];

    const pills = [
      `<button type="button" class="filter-pill active" data-filter-value="all">전체</button>`,
      ...categories.map(
        (c) =>
          `<button type="button" class="filter-pill" data-filter-value="${escapeHTML(c)}">${escapeHTML(c)}</button>`
      )
    ];
    container.innerHTML = pills.join('');

    // 대분류 안의 세부 장르 pill 행을 채우거나 숨긴다.
    function showSubGenres(category) {
      if (!subContainer || !subGroup) return;

      const subs = genreGroups.get(category) || [];
      if (subs.length <= 1) {
        subGroup.hidden = true;
        subContainer.innerHTML = '';
        return;
      }

      const subPills = [
        `<button type="button" class="filter-pill active" data-filter-value="all">전체</button>`,
        ...subs.map(
          (g) =>
            `<button type="button" class="filter-pill" data-filter-value="${escapeHTML(g)}">${escapeHTML(g)}</button>`
        )
      ];
      subContainer.innerHTML = subPills.join('');
      subGroup.hidden = false;
    }

    // 대분류 클릭
    container.addEventListener('click', (e) => {
      const pill = e.target.closest('.filter-pill');
      if (!pill || !container.contains(pill)) return;

      const value = pill.dataset.filterValue;
      activeFilters.genreCategory = value;
      activeFilters.genreSub = 'all';

      container
        .querySelectorAll('.filter-pill')
        .forEach((p) => p.classList.toggle('active', p === pill));

      if (value === 'all') {
        if (subGroup) subGroup.hidden = true;
        if (subContainer) subContainer.innerHTML = '';
      } else {
        showSubGenres(value);
      }

      applyFilters();
    });

    // 세부 장르 클릭 (이벤트 위임 — 컨테이너는 항상 존재, 내용만 갱신됨)
    if (subContainer) {
      subContainer.addEventListener('click', (e) => {
        const pill = e.target.closest('.filter-pill');
        if (!pill || !subContainer.contains(pill)) return;

        activeFilters.genreSub = pill.dataset.filterValue;

        subContainer
          .querySelectorAll('.filter-pill')
          .forEach((p) => p.classList.toggle('active', p === pill));

        applyFilters();
      });
    }
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
      if (activeFilters.genreCategory !== 'all') {
        if (genreCategoryOf(album.genre) !== activeFilters.genreCategory) return false;
        if (activeFilters.genreSub !== 'all' && album.genre !== activeFilters.genreSub) return false;
      }
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
  buildGenreFilter();
  buildFilterGroup('format', 'format');
  buildYearFilter();

  renderCards(albums);
})();
