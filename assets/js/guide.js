/* ============================================================
   SLEEVE — guide.js
   guide.html 전용. 바이닐 가이드 게시판형 목록 + 인라인 아코디언.
   - guide.json 로드
   - 카테고리 필터 탭 (전체 / 입문 / 관리 / 장비 / 문화)
   - 목록 클릭 시 본문 인라인 펼침 (한 번에 하나만 열림)
   - 빈 결과 시 empty-state 폴백
   ============================================================ */
'use strict';

(async () => {
  const { escapeHTML, formatDate } = window.SLEEVE;

  const items = await SLEEVE.loadGuide();

  const filterBar = document.getElementById('guideFilter');
  const list = document.getElementById('guideList');
  const empty = document.getElementById('guideEmpty');

  // 카테고리 영문 키 → 한글 레이블
  const CATEGORY_LABELS = {
    beginner: '입문',
    care: '관리',
    equipment: '장비',
    culture: '문화'
  };

  // 필터 탭 순서 (전체 + 데이터에 실제로 존재하는 카테고리만)
  const FILTER_ORDER = ['beginner', 'care', 'equipment', 'culture'];

  let activeCategory = 'all'; // 'all' 또는 카테고리 키

  /* --------------------------------------------------------
     카테고리 한글 레이블 (미정의 카테고리는 원본 그대로)
     -------------------------------------------------------- */
  function labelFor(category) {
    return CATEGORY_LABELS[category] || category || '기타';
  }

  /* --------------------------------------------------------
     1. 필터 탭 생성
     데이터에 존재하는 카테고리만 노출 (빈 탭 방지).
     -------------------------------------------------------- */
  function buildFilter() {
    if (!filterBar) return;

    const present = FILTER_ORDER.filter((cat) =>
      items.some((it) => it.category === cat)
    );

    const buttons = [
      `<button type="button" class="filter-pill active" data-category="all">전체</button>`,
      ...present.map(
        (cat) =>
          `<button type="button" class="filter-pill" data-category="${escapeHTML(
            cat
          )}">${escapeHTML(labelFor(cat))}</button>`
      )
    ];

    filterBar.innerHTML = buttons.join('');

    // 이벤트 위임
    filterBar.addEventListener('click', (e) => {
      const btn = e.target.closest('.filter-pill');
      if (!btn || !filterBar.contains(btn)) return;

      activeCategory = btn.dataset.category;

      filterBar
        .querySelectorAll('.filter-pill')
        .forEach((b) => b.classList.toggle('active', b === btn));

      renderList();
    });
  }

  /* --------------------------------------------------------
     2. 목록 렌더 (아코디언)
     body 텍스트는 escape 후 줄바꿈만 <br> 로 복원.
     -------------------------------------------------------- */
  function renderList() {
    if (!list) return;

    const filtered =
      activeCategory === 'all'
        ? items
        : items.filter((it) => it.category === activeCategory);

    if (!filtered.length) {
      list.innerHTML = '';
      if (empty) empty.hidden = false;
      return;
    }

    if (empty) empty.hidden = true;

    list.innerHTML = filtered
      .map((it) => {
        const bodyId = `guide-body-${escapeHTML(it.id)}`;
        const bodyHtml = escapeHTML(it.body || '').replace(/\r?\n/g, '<br>');
        return `
      <li class="guide-list__item">
        <button class="guide-list__toggle" type="button" aria-expanded="false" aria-controls="${bodyId}">
          <span class="guide-list__title">${escapeHTML(it.title)}</span>
          <span class="badge-category">${escapeHTML(labelFor(it.category))}</span>
          <span class="guide-list__date">${escapeHTML(formatDate(it.created_at))}</span>
        </button>
        <div class="guide-list__body" id="${bodyId}" hidden>
          <p>${bodyHtml}</p>
        </div>
      </li>`;
      })
      .join('');
  }

  /* --------------------------------------------------------
     3. 아코디언 토글 (이벤트 위임)
     한 번에 하나만 열림: 다른 항목을 열면 나머지는 닫는다.
     -------------------------------------------------------- */
  function initAccordion() {
    if (!list) return;

    list.addEventListener('click', (e) => {
      const toggle = e.target.closest('.guide-list__toggle');
      if (!toggle || !list.contains(toggle)) return;

      const isOpen = toggle.getAttribute('aria-expanded') === 'true';

      // 모든 항목 닫기
      list.querySelectorAll('.guide-list__toggle').forEach((btn) => {
        btn.setAttribute('aria-expanded', 'false');
        const body = document.getElementById(
          btn.getAttribute('aria-controls')
        );
        if (body) body.hidden = true;
      });

      // 닫혀 있던 것을 클릭했다면 해당 항목만 열기 (토글 동작)
      if (!isOpen) {
        toggle.setAttribute('aria-expanded', 'true');
        const body = document.getElementById(
          toggle.getAttribute('aria-controls')
        );
        if (body) body.hidden = false;
      }
    });
  }

  /* --------------------------------------------------------
     초기화
     -------------------------------------------------------- */
  buildFilter();
  renderList();
  initAccordion();
})();
