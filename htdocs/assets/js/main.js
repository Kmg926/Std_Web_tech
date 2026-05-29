/* =========================================================================
   SLEEVE — Main JavaScript
   Frontend interactions: filters, navbar scroll, smooth scroll,
   lazy loading, and lightweight canvas bar charts.
   ========================================================================= */

(function () {
  'use strict';

  /* -----------------------------------------------------------------------
     Utility helpers
     ----------------------------------------------------------------------- */
  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  const throttle = (fn, wait = 100) => {
    let last = 0;
    let timer = null;
    return function throttled(...args) {
      const now = Date.now();
      const remaining = wait - (now - last);
      if (remaining <= 0) {
        if (timer) {
          clearTimeout(timer);
          timer = null;
        }
        last = now;
        fn.apply(this, args);
      } else if (!timer) {
        timer = setTimeout(() => {
          last = Date.now();
          timer = null;
          fn.apply(this, args);
        }, remaining);
      }
    };
  };

  /* -----------------------------------------------------------------------
     1. Filter system for collection.php
        - .filter-pill[data-filter-group][data-filter-value]
        - "전체" → data-filter-value="all"
        - .sleeve-card[data-genre][data-format][data-year]
        ----------------------------------------------------------------------- */
  function initCollectionFilters() {
    const filterBar = $('[data-filter-bar]');
    if (!filterBar) return;

    const cards = $$('[data-filter-target] .sleeve-card, .sleeve-card[data-genre], .sleeve-card[data-format], .sleeve-card[data-year]');
    if (cards.length === 0) return;

    // Active filter state: { genre: 'all', format: 'all', year: 'all' }
    const activeFilters = Object.create(null);

    // Initialize groups based on rendered pill groups
    $$('.filter-pill', filterBar).forEach((pill) => {
      const group = pill.dataset.filterGroup;
      if (!group) return;
      if (!(group in activeFilters)) {
        activeFilters[group] = 'all';
      }
    });

    function applyFilters() {
      cards.forEach((card) => {
        const matches = Object.entries(activeFilters).every(([group, value]) => {
          if (value === 'all') return true;
          const cardValue = (card.dataset[group] || '').toLowerCase();
          return cardValue === value.toLowerCase();
        });

        // Toggle visibility — use hidden attribute for accessibility
        if (matches) {
          card.hidden = false;
          card.style.display = '';
        } else {
          card.hidden = true;
          card.style.display = 'none';
        }
      });

      // Empty-state messaging
      const emptyState = $('[data-filter-empty]');
      if (emptyState) {
        const anyVisible = cards.some((card) => !card.hidden);
        emptyState.hidden = anyVisible;
      }
    }

    filterBar.addEventListener('click', (event) => {
      const pill = event.target.closest('.filter-pill');
      if (!pill || !filterBar.contains(pill)) return;

      const group = pill.dataset.filterGroup;
      const value = pill.dataset.filterValue;
      if (!group || value == null) return;

      // Update state
      activeFilters[group] = value;

      // Update visual active state within the group
      $$(`.filter-pill[data-filter-group="${group}"]`, filterBar).forEach((p) => {
        p.classList.toggle('active', p === pill);
        p.setAttribute('aria-pressed', p === pill ? 'true' : 'false');
      });

      applyFilters();
    });

    // Initial state — apply on load (in case server rendered active pills)
    applyFilters();
  }

  /* -----------------------------------------------------------------------
     2. Navbar scroll state
        ----------------------------------------------------------------------- */
  function initNavbarScroll() {
    const navbar = $('.navbar-sleeve');
    if (!navbar) return;

    const updateState = () => {
      const scrolled = window.scrollY > 50;
      navbar.classList.toggle('scrolled', scrolled);
    };

    updateState();
    window.addEventListener('scroll', throttle(updateState, 100), { passive: true });
  }

  /* -----------------------------------------------------------------------
     3. Smooth scroll for in-page anchor links
        ----------------------------------------------------------------------- */
  function initSmoothScroll() {
    document.addEventListener('click', (event) => {
      const link = event.target.closest('a[href^="#"]');
      if (!link) return;

      const href = link.getAttribute('href');
      if (!href || href === '#' || href.length < 2) return;

      const target = document.getElementById(href.slice(1));
      if (!target) return;

      event.preventDefault();

      const navbar = $('.navbar-sleeve');
      const offset = navbar ? navbar.offsetHeight + 12 : 0;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;

      window.scrollTo({
        top,
        behavior: 'smooth'
      });

      // Move focus for accessibility
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
    });
  }

  /* -----------------------------------------------------------------------
     4. Lazy load images via IntersectionObserver
        Usage: <img data-src="path.jpg" alt="...">
        ----------------------------------------------------------------------- */
  function initLazyImages() {
    const lazyImages = $$('img[data-src]');
    if (lazyImages.length === 0) return;

    // Fallback for very old browsers
    if (!('IntersectionObserver' in window)) {
      lazyImages.forEach((img) => {
        img.src = img.dataset.src;
        if (img.dataset.srcset) img.srcset = img.dataset.srcset;
      });
      return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        const img = entry.target;
        if (img.dataset.src) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        }
        if (img.dataset.srcset) {
          img.srcset = img.dataset.srcset;
          img.removeAttribute('data-srcset');
        }
        img.addEventListener('load', () => img.classList.add('is-loaded'), { once: true });
        obs.unobserve(img);
      });
    }, {
      rootMargin: '200px 0px',
      threshold: 0.01
    });

    lazyImages.forEach((img) => observer.observe(img));
  }

  /* -----------------------------------------------------------------------
     5. Lightweight canvas bar chart
        Public API: window.SLEEVE.renderBarChart(canvasId, labels, data, color)
        ----------------------------------------------------------------------- */
  function renderBarChart(canvasId, labels, data, color) {
    const canvas = typeof canvasId === 'string'
      ? document.getElementById(canvasId)
      : canvasId;
    if (!canvas || !canvas.getContext) return;
    if (!Array.isArray(labels) || !Array.isArray(data) || labels.length === 0) return;

    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;

    // Resize for crisp rendering
    const rect = canvas.getBoundingClientRect();
    const cssWidth = rect.width || canvas.clientWidth || 320;
    const cssHeight = rect.height || canvas.clientHeight || 220;
    canvas.width = Math.round(cssWidth * dpr);
    canvas.height = Math.round(cssHeight * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    // Clear
    ctx.clearRect(0, 0, cssWidth, cssHeight);

    // Layout
    const padding = { top: 16, right: 12, bottom: 28, left: 32 };
    const chartW = cssWidth - padding.left - padding.right;
    const chartH = cssHeight - padding.top - padding.bottom;
    const maxValue = Math.max.apply(null, data);
    const niceMax = niceCeil(maxValue);
    const barColor = color || '#D98A3D';
    const axisColor = 'rgba(247, 243, 236, 0.2)';
    const labelColor = '#8A7D6E';

    // Axis baseline
    ctx.strokeStyle = axisColor;
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(padding.left, padding.top + chartH + 0.5);
    ctx.lineTo(padding.left + chartW, padding.top + chartH + 0.5);
    ctx.stroke();

    // Y-axis gridlines (3 ticks)
    ctx.font = '11px Inter, system-ui, sans-serif';
    ctx.fillStyle = labelColor;
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';

    const tickCount = 3;
    for (let i = 0; i <= tickCount; i++) {
      const value = (niceMax / tickCount) * i;
      const y = padding.top + chartH - (value / niceMax) * chartH;

      // Gridline
      if (i > 0) {
        ctx.strokeStyle = 'rgba(247, 243, 236, 0.06)';
        ctx.beginPath();
        ctx.moveTo(padding.left, y + 0.5);
        ctx.lineTo(padding.left + chartW, y + 0.5);
        ctx.stroke();
      }

      // Label
      ctx.fillText(String(Math.round(value)), padding.left - 6, y);
    }

    // Bars
    const gap = 8;
    const barWidth = Math.max(4, (chartW - gap * (labels.length - 1)) / labels.length);

    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';

    labels.forEach((label, i) => {
      const value = data[i] || 0;
      const barH = niceMax === 0 ? 0 : (value / niceMax) * chartH;
      const x = padding.left + i * (barWidth + gap);
      const y = padding.top + chartH - barH;

      // Bar (rounded top corners)
      drawRoundedBar(ctx, x, y, barWidth, barH, 3, barColor);

      // X-axis label
      ctx.fillStyle = labelColor;
      ctx.fillText(truncate(label, 8), x + barWidth / 2, padding.top + chartH + 8);

      // Value on top of bar (if it fits)
      if (barH > 18) {
        ctx.fillStyle = '#F7F3EC';
        ctx.font = '600 11px Inter, system-ui, sans-serif';
        ctx.fillText(String(value), x + barWidth / 2, y + 4);
        ctx.font = '11px Inter, system-ui, sans-serif';
      }
    });
  }

  function drawRoundedBar(ctx, x, y, w, h, r, color) {
    if (h <= 0) return;
    const radius = Math.min(r, w / 2, h);

    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.moveTo(x, y + h);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.lineTo(x + w - radius, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + radius);
    ctx.lineTo(x + w, y + h);
    ctx.closePath();
    ctx.fill();
  }

  function niceCeil(value) {
    if (!isFinite(value) || value <= 0) return 1;
    const exponent = Math.floor(Math.log10(value));
    const fraction = value / Math.pow(10, exponent);
    let niceFraction;
    if (fraction <= 1) niceFraction = 1;
    else if (fraction <= 2) niceFraction = 2;
    else if (fraction <= 5) niceFraction = 5;
    else niceFraction = 10;
    return niceFraction * Math.pow(10, exponent);
  }

  function truncate(str, max) {
    const s = String(str);
    return s.length > max ? s.slice(0, max - 1) + '…' : s;
  }

  // Re-render charts on resize (debounced)
  function initChartResize() {
    let timer = null;
    window.addEventListener('resize', () => {
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => {
        if (typeof window.SLEEVE.onChartResize === 'function') {
          window.SLEEVE.onChartResize();
        }
      }, 150);
    });
  }

  /* -----------------------------------------------------------------------
     Public API
     ----------------------------------------------------------------------- */
  window.SLEEVE = window.SLEEVE || {};
  window.SLEEVE.renderBarChart = renderBarChart;

  /* -----------------------------------------------------------------------
     Bootstrap on DOM ready
     ----------------------------------------------------------------------- */
  function init() {
    initCollectionFilters();
    initNavbarScroll();
    initSmoothScroll();
    initLazyImages();
    initChartResize();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
