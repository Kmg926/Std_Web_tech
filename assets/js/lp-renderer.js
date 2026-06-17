/* ============================================================
   SLEEVE — lp-renderer.js
   가상 LP(Virtual LP) SVG 렌더러.
   lp_config 객체를 받아 innerHTML 로 주입할 SVG 문자열을 반환한다.
   components.js 다음, 페이지 인라인 스크립트 이전에 로드된다.
   window.SLEEVE.renderLP(config) 로 공개.
   ============================================================ */
(function () {
  'use strict';

  // components.js 가 먼저 로드되면 SLEEVE.escapeHTML 을 쓰지만,
  // 로드 순서에 의존하지 않도록 동일 규칙(& < > " ')의 폴백을 둔다.
  function localEscape(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function renderLP(config) {
    if (!config) return '';

    const SL = window.SLEEVE;
    const esc = (SL && typeof SL.escapeHTML === 'function') ? SL.escapeHTML : localEscape;

    // 색상값은 fill 속성에 들어가므로 누락 시 안전한 기본값으로 대체한다.
    const labelColor = config.label_color || '#111';
    const labelTextColor = config.label_color_text || '#fff';

    const primary = esc(config.label_primary || '');
    const secondary = esc(config.label_secondary || '');
    const side = esc(config.label_side || '');

    const ariaLabel = esc((config.label_primary || 'LP') + ' 가상 LP');

    // vinyl_type 분기: clear = 투명 바이닐, 그 외 = 블랙 바이닐 폴백.
    const isClear = config.vinyl_type === 'clear';

    const palette = isClear
      ? {
          vinylFill: 'rgba(180,210,235,0.13)',
          vinylStroke: 'rgba(180,210,235,0.35)',
          groove: 'rgba(180,210,235,0.18)',
          deadFill: 'rgba(180,210,235,0.08)',
          deadStroke: 'rgba(180,210,235,0.25)',
          sheen: 'rgba(180,210,235,0.5)'
        }
      : {
          vinylFill: '#111',
          vinylStroke: 'rgba(255,255,255,0.18)',
          groove: 'rgba(255,255,255,0.07)',
          deadFill: '#1a1a1a',
          deadStroke: 'rgba(255,255,255,0.12)',
          sheen: 'rgba(255,255,255,0.22)'
        };

    // 고유 그라디언트 id (한 페이지에 여러 LP가 있어도 충돌 없도록).
    const gid = 'lp-sheen-' + Math.random().toString(36).slice(2, 9);

    // 그루브: r 80 → 143 사이를 균등 분할한 동심원 15개.
    const grooveCount = 15;
    let grooves = '';
    for (let i = 0; i < grooveCount; i++) {
      const r = 80 + (143 - 80) * (i / (grooveCount - 1));
      grooves += `<circle cx="150" cy="150" r="${r.toFixed(1)}" fill="none" stroke="${palette.groove}" stroke-width="0.6"/>`;
    }

    return `
      <svg viewBox="0 0 300 300" class="lp-svg" role="img" aria-label="${ariaLabel}" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <radialGradient id="${gid}" cx="38%" cy="32%" r="75%">
            <stop offset="0%" stop-color="${palette.sheen}" stop-opacity="0.45"/>
            <stop offset="45%" stop-color="${palette.sheen}" stop-opacity="0.08"/>
            <stop offset="100%" stop-color="${palette.sheen}" stop-opacity="0"/>
          </radialGradient>
        </defs>

        <circle cx="150" cy="150" r="145" fill="${palette.vinylFill}" stroke="${palette.vinylStroke}" stroke-width="1"/>
        <circle cx="150" cy="150" r="145" fill="url(#${gid})"/>

        ${grooves}

        <circle cx="150" cy="150" r="55" fill="${palette.deadFill}" stroke="${palette.deadStroke}" stroke-width="0.8"/>

        <circle cx="150" cy="150" r="50" fill="${esc(labelColor)}"/>
        <text x="150" y="146" text-anchor="middle" font-family="Inter, sans-serif" font-weight="bold" font-size="14" fill="${esc(labelTextColor)}">${primary}</text>
        <text x="150" y="162" text-anchor="middle" font-family="Inter, sans-serif" font-size="10" fill="${esc(labelTextColor)}">${secondary}</text>
        <text x="150" y="178" text-anchor="middle" font-family="Inter, sans-serif" font-size="8" fill="${esc(labelTextColor)}" opacity="0.8">${side}</text>

        <circle cx="150" cy="150" r="4" fill="rgba(0,0,0,0.55)"/>

        <path d="M 235 88 A 120 120 0 0 1 258 158" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2" stroke-linecap="round"/>
      </svg>`;
  }

  // 기존 SLEEVE 키를 덮어쓰지 않고 renderLP 만 추가한다.
  window.SLEEVE = window.SLEEVE || {};
  window.SLEEVE.renderLP = renderLP;
})();
