/* ============================================================
   SLEEVE — components.js
   모든 페이지가 로드하는 공통 스크립트.
   - navbar/footer 주입 (includes/*.html fetch)
   - 네비게이션 인터랙션 (토글, 스크롤, active 표시)
   - 이미지 lazy load (IntersectionObserver)
   - 공용 유틸 (escapeHTML, formatDate)
   - JSON 로더 유틸 (albums / designs / guide)
   - window.SLEEVE 네임스페이스로 공개
   ============================================================ */
'use strict';

/* ----------------------------------------------------------
   A. navbar / footer 주입
   ---------------------------------------------------------- */
async function loadComponent(selector, url) {
  const el = document.querySelector(selector);
  if (!el) return;
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(res.status);
    el.innerHTML = await res.text();
  } catch (e) {
    console.warn('Component load failed:', url, e);
  }
}

/* ----------------------------------------------------------
   B. initNavbar — 토글 / 스크롤 / active 처리
   ---------------------------------------------------------- */
function initNavbar() {
  const navbar = document.getElementById('navbar');
  const toggle = document.querySelector('.navbar__toggle');
  const menu = document.getElementById('navMenu');

  // 햄버거 토글 (모바일)
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      const isOpen = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });
  }

  // 스크롤 시 navbar 축소/그림자 클래스
  if (navbar) {
    const onScroll = () => {
      navbar.classList.toggle('navbar--scrolled', window.scrollY > 50);
    };
    // passive: 스크롤 성능 (브라우저가 preventDefault 없음을 보장)
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // 초기 상태 반영 (새로고침 시 스크롤 위치 유지 대비)
  }

  // 현재 페이지 active 표시
  setActiveNav();
}

/**
 * location.pathname 의 basename 으로 현재 페이지를 감지해
 * 해당 data-nav 링크(또는 로고)에 .active 클래스를 부여한다.
 * 상세 페이지(album.html / design.html)는 부모 목록을 active 처리.
 */
function setActiveNav() {
  // 경로 끝의 파일명 추출. "/" 또는 빈 값이면 index.html 로 간주.
  const path = location.pathname.split('/').pop() || 'index.html';
  const page = path === '' ? 'index.html' : path;

  // 상세 페이지를 목록 탭에 매핑
  const aliasMap = {
    'album.html': 'collection',
    'design.html': 'designs'
  };

  const logo = document.querySelector('.navbar__logo');

  if (page === 'index.html') {
    if (logo) logo.classList.add('active');
    return;
  }

  // 파일명에서 ".html" 제거한 키 또는 alias 키
  const key = aliasMap[page] || page.replace(/\.html$/, '');
  const link = document.querySelector(`[data-nav="${key}"]`);
  if (link) link.classList.add('active');
}

/* ----------------------------------------------------------
   C. 이미지 lazy load
   data-src 가 있는 <img> 를 뷰포트 근처에서 실제 로드.
   IntersectionObserver 미지원 환경에서는 즉시 로드 폴백.
   ---------------------------------------------------------- */
function initLazyLoad() {
  const imgs = document.querySelectorAll('img[data-src]');
  if (!imgs.length) return;

  // 폴백: IO 미지원이면 즉시 로드
  if (!('IntersectionObserver' in window)) {
    imgs.forEach((img) => {
      img.src = img.dataset.src;
      img.removeAttribute('data-src');
    });
    return;
  }

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.src = e.target.dataset.src;
          e.target.removeAttribute('data-src');
          io.unobserve(e.target);
        }
      });
    },
    { rootMargin: '200px' }
  );

  imgs.forEach((img) => io.observe(img));
}

/* ----------------------------------------------------------
   D. escapeHTML — XSS 방지 헬퍼
   innerHTML 에 동적 문자열을 넣기 전 반드시 통과시킨다.
   ---------------------------------------------------------- */
function escapeHTML(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/* ----------------------------------------------------------
   E. formatDate — "2024-01-15" → "2024.01.15"
   파싱 불가하거나 빈 값이면 원본 문자열을 안전하게 반환.
   ---------------------------------------------------------- */
function formatDate(dateStr) {
  if (!dateStr) return '';
  const str = String(dateStr);
  const m = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (!m) return str;
  return `${m[1]}.${m[2]}.${m[3]}`;
}

/* ----------------------------------------------------------
   G. JSON 로더 유틸
   각 페이지 스크립트가 SLEEVE.loadAlbums() 등으로 호출.
   로드 실패 시 빈 배열을 반환해 호출부가 빈 상태 UI로 폴백하게 함.
   ---------------------------------------------------------- */
async function loadJSON(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(res.status);
    return await res.json();
  } catch (e) {
    console.warn('Data load failed:', url, e);
    return [];
  }
}

async function loadAlbums() {
  return loadJSON('assets/data/albums.json');
}

async function loadDesigns() {
  return loadJSON('assets/data/designs.json');
}

async function loadGuide() {
  return loadJSON('assets/data/guide.json');
}

/* ----------------------------------------------------------
   F. window.SLEEVE 네임스페이스 공개
   ---------------------------------------------------------- */
window.SLEEVE = {
  escapeHTML,
  formatDate,
  loadAlbums,
  loadDesigns,
  loadGuide
};

// 페이지 스크립트가 렌더 후 재호출할 수 있도록 전역 노출
window.initLazyLoad = initLazyLoad;

/* ----------------------------------------------------------
   부트스트랩
   navbar/footer 주입 → initNavbar → 초기 lazy load
   ---------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', async () => {
  await Promise.all([
    loadComponent('#navbar-placeholder', 'includes/navbar.html'),
    loadComponent('#footer-placeholder', 'includes/footer.html')
  ]);

  initNavbar();
  initLazyLoad();
});
