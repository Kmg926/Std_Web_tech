/* ============================================================
   SLEEVE — stats.js
   stats.html 전용. 컬렉션 통계 + Chart.js 바 차트.
   - albums.json 집계 (format / genre / year)
   - 요약 카드 (총 / Vinyl / CD)
   - 장르 차트(앰버), 연도 차트(틸)
   Chart.js 는 CDN 으로 선로드되어 있다고 가정.
   ============================================================ */
'use strict';

(async () => {
  const albums = await SLEEVE.loadAlbums();

  /* --------------------------------------------------------
     집계
     -------------------------------------------------------- */
  const total = albums.length;

  const byFormat = albums.reduce((acc, a) => {
    acc[a.format] = (acc[a.format] || 0) + 1;
    return acc;
  }, {});

  const byGenre = albums.reduce((acc, a) => {
    acc[a.genre] = (acc[a.genre] || 0) + 1;
    return acc;
  }, {});

  const byYear = albums.reduce((acc, a) => {
    acc[a.release_year] = (acc[a.release_year] || 0) + 1;
    return acc;
  }, {});

  /* --------------------------------------------------------
     요약 카드 (textContent 사용 — XSS 무관, 숫자만)
     -------------------------------------------------------- */
  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = String(value);
  };

  setText('statTotal', total);
  setText('statVinyl', byFormat['Vinyl'] || 0);
  setText('statCD', byFormat['CD'] || 0);

  /* --------------------------------------------------------
     Chart.js 가드: CDN 로드 실패 시 조용히 중단
     -------------------------------------------------------- */
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js not loaded — charts skipped.');
    return;
  }

  // 데이터가 비면 차트 생성 생략 (빈 캔버스 방지)
  if (!total) return;

  /* --------------------------------------------------------
     공통 차트 옵션
     -------------------------------------------------------- */
  const chartOpts = () => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: {
        ticks: { color: '#8A7D6E' },
        grid: { color: 'rgba(226,216,200,0.08)' }
      },
      y: {
        ticks: { color: '#8A7D6E', stepSize: 1 },
        grid: { color: 'rgba(226,216,200,0.08)' },
        beginAtZero: true
      }
    }
  });

  /* --------------------------------------------------------
     장르 차트 (앰버)
     -------------------------------------------------------- */
  const genreCanvas = document.getElementById('genreChart');
  if (genreCanvas) {
    new Chart(genreCanvas, {
      type: 'bar',
      data: {
        labels: Object.keys(byGenre),
        datasets: [
          {
            data: Object.values(byGenre),
            backgroundColor: '#D98A3D',
            borderRadius: 4
          }
        ]
      },
      options: chartOpts()
    });
  }

  /* --------------------------------------------------------
     연도 차트 (틸) — 연도 오름차순 정렬
     -------------------------------------------------------- */
  const yearCanvas = document.getElementById('yearChart');
  if (yearCanvas) {
    const years = Object.keys(byYear).sort();
    new Chart(yearCanvas, {
      type: 'bar',
      data: {
        labels: years,
        datasets: [
          {
            data: years.map((y) => byYear[y]),
            backgroundColor: '#5C8374',
            borderRadius: 4
          }
        ]
      },
      options: chartOpts()
    });
  }
})();
