# SLEEVE

LP·CD 컬렉션과 직접 만든 자켓 디자인을 기록하고 전시하는 개인 바이닐 아카이브 + 포트폴리오 사이트입니다.

대학 "표준웹테크놀러지" 기말 프로젝트로, 프레임워크나 빌드 도구 없이 순수 정적 사이트(HTML + CSS + Vanilla JavaScript)로 구현했습니다. JavaScript가 정적 JSON 데이터를 읽어 페이지를 렌더링하는 멀티페이지 구조이며, 판매·회원·로그인 기능 없이 "전시와 기록"에 집중합니다.

## 핵심 가치

- **수집(Archive)** — 보유 음반을 자켓·트랙리스트·감상평과 함께 기록
- **창작(Portfolio)** — 직접 만든 자켓/바이닐 디자인을 의도·과정과 함께 전시
- **공유(Curation)** — 무드·테마별 추천과 입문 가이드로 바이닐 문화 소개

## 주요 기능

- `albums.json` 기반 앨범 카드 피드 (앨범 35개)
- 클라이언트 사이드 필터링 (장르·포맷·연도)
- `?id=` 쿼리스트링 라우팅으로 단일 템플릿에서 다수 상세 페이지 구현
- 무드별 큐레이션 (페이지 로드마다 랜덤 셔플)
- `IntersectionObserver` 기반 이미지 lazy load
- 사인반·부틀렉 배지 시스템
- Chart.js를 활용한 컬렉션 통계 시각화
- 반응형 레이아웃 (360 / 768 / 1200px)
- 잘못된 `?id=`·빈 데이터에 대한 인페이지 폴백 처리

## Tech Stack

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=flat-square&logo=chartdotjs&logoColor=white)
![Google Fonts](https://img.shields.io/badge/Google_Fonts-4285F4?style=flat-square&logo=google&logoColor=white)

| 구분 | 사용 기술 |
| --- | --- |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Data | 정적 JSON (`assets/data/`) |
| Charts | Chart.js (CDN) |
| Fonts | Playfair Display, Inter, Noto Sans KR (Google Fonts) |
| Backend / DB | 없음 (순수 정적 사이트) |

## 페이지 구성

| 페이지 | 파일 | 설명 |
| --- | --- | --- |
| 메인 | `index.html` | 사이드바 + 피드 레이아웃, 최근 추가 앨범과 글 카드 |
| 소개 | `about.html` | 아카이브 취지·운영자 소개 |
| 컬렉션 목록 | `collection.html` | 장르·포맷·연도 필터 + 카드 그리드 |
| 앨범 상세 | `album.html?id=` | 자켓·트랙리스트·감상평·관련 음반 |
| 디자인 갤러리 | `designs.html` | 직접 만든 자켓 디자인 갤러리 |
| 디자인 상세 | `design.html?id=` | 대표 이미지·제작 의도·과정 |
| 큐레이션 | `curation.html` | 테마/무드별 추천 (랜덤 셔플) |
| 가이드 | `guide.html` | 바이닐 입문 가이드 |
| 통계 | `stats.html` | 장르·연도·포맷 차트 (Chart.js) |
| 문의 | `contact.html` | 이메일·SNS 링크 + FAQ |

상세 페이지는 개별 파일 대신 **단일 템플릿 + `?id=` 쿼리스트링 + JS 렌더** 방식으로 구현했습니다.

## 디렉터리 구조

```
├── *.html                # 페이지 (index, about, collection, album, designs, design, curation, guide, stats, contact)
├── assets/
│   ├── css/              # tokens.css, base.css, components.css, pages.css
│   ├── js/               # components.js, collection.js, album.js, curation.js, guide.js, stats.js
│   ├── data/             # albums.json, designs.json, guide.json, faq.json
│   └── img/              # covers/, designs/, ui/
└── includes/             # navbar.html, footer.html (fetch 주입)
```

공통 영역인 navbar·footer는 `includes/`의 마크업을 JS(`components.js`)로 주입해 모든 페이지에서 재사용합니다.

## 데이터 모델

DB 없이 `assets/data/` 아래 정적 JSON으로 관리합니다. 추후 RDB로 확장할 때 그대로 매핑되도록 컬럼명을 보존했습니다.

- `albums.json` — 앨범 (id, title, artist, release_year, format, genre, cover_path, note, moods, tracks 등)
- `designs.json` — 자켓 디자인 (id, title, tools, intent, process, images 등)
- `guide.json` — 가이드 글 (id, title, category, body 등)
- `faq.json` — 자주 묻는 질문 (q, a)

각 페이지의 JS가 `fetch()`로 해당 JSON을 읽어 렌더링합니다.

## 디자인

다크 배경 + 앰버 포인트의 아날로그 감성으로, 자켓 이미지가 주인공이 되도록 설계했습니다. 디자인 토큰은 `assets/css/tokens.css`의 `:root`에 정의되어 있습니다.

| 토큰 | 값 | 용도 |
| --- | --- | --- |
| `--ink` | `#1C1714` | 기본 텍스트·다크 배경 |
| `--paper` | `#F7F3EC` | 밝은 배경 |
| `--amber` | `#D98A3D` | 포인트 컬러 |
| `--teal` | `#5C8374` | 보조 컬러 |

## 로컬 실행

`fetch()`로 JSON·include를 로드하므로 `file://`로 직접 열 수 없습니다. 로컬 정적 서버를 실행해 주세요.

```bash
python -m http.server 8080
# 또는
npx serve .
```

이후 브라우저에서 접속합니다.

```
http://localhost:8080
```
