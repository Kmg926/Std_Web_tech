## System Prompt
명령어 일일이 yes 누르기 귀찮으니까 settings에 자동으로 승인되도록 등록해줘
commit은 conventional commit 형식을 지켜줘
EX) Feat(fe): Login Modal
클로드코드 기여 내용을 절대 어디에도 포함하면 안돼
서브 에이전트들 보이지? 얘네들 보고 코드를 작성하라고 하고 너는 PM으로서 나와 대화하고 그 내용을 기반으로 서브에이전트에게 명령만 내려
GH cli 깔려있음

## Tech Stack
Frontend: HTML, CSS, JS
Backend: PHP
Hosting: infinityfree
DB: MySQL

## Project 개요
이름 / 한 줄 소개
SLEEVE — 내가 모은 LP·CD와 직접 만든 자켓 디자인을 한곳에 기록·전시하는 개인 바이닐 아카이브 + 포트폴리오 사이트.
성격
PHP가 MySQL 데이터를 읽어 페이지를 렌더링하는 동적 멀티페이지 사이트. 판매·회원·로그인·문의 기능 없음. 목적은 "전시와 기록". (대학 "표준웹테크놀러지" 기말 프로젝트 / 부트스트랩 카드 그리드형 템플릿을 SLEEVE 톤으로 변형 / 최소 10페이지 이상)
핵심 가치

수집(Archive): 보유 음반을 자켓·트랙리스트·감상평과 함께 기록
창작(Portfolio): 직접 만든 자켓/바이닐 디자인을 의도·과정과 함께 전시
공유(Curation): 무드·테마별 추천과 입문 가이드로 바이닐 문화 소개

페이지 구성 (총 10 — 10페이지 요건 충족)

index.php — 메인 (Hero + 하이라이트 카드)
about.php — 소개 (아카이브 취지·운영자)
collection.php — 컬렉션 목록 (장르·포맷·연도 필터 + 카드 그리드)
album.php?id= — 앨범 상세 (자켓·트랙리스트·감상평·관련 음반)
designs.php — 자켓 디자인 갤러리
design.php?id= — 디자인 상세 (이미지·제작 의도·과정)
curation.php — 테마/무드 큐레이션 ("오늘의 한 장")
guide.php — 바이닐 가이드 (게시판형)
stats.php — 컬렉션 통계 (장르·연도·포맷 차트)
404.php — 없는 라우트·잘못된 id 처리

데이터 모델 (MySQL, utf8mb4)

albums(id, slug, title, artist, release_year, format[Vinyl|CD], genre, cover_path, note, is_limited, created_at)
tracks(id, album_id→albums, position, title)
album_moods(album_id→albums, mood)  — 큐레이션 분류
designs(id, slug, title, tools, thumb_path, intent, process, created_at)
design_images(id, design_id→designs, image_path, sort_order)
guide_posts(id, slug, title, category, body, created_at)

디자인 방향

톤: 다크 배경 + 앰버 포인트의 아날로그 감성, 자켓 이미지가 주인공
토큰(:root): --ink:#1C1714 / --paper:#F7F3EC / --cream:#F2E9DA / --amber:#D98A3D / --amber-d:#B36C28 / --teal:#5C8374 / --muted:#8A7D6E / --line:#E2D8C8
폰트: 헤드라인 세리프(Playfair Display/Lora) + 본문 Inter + 한글 Noto Sans KR
자켓 카드는 정사각(1:1), 한정반은 앰버 배지, LP판(동심원) 모티프를 포인트로
반응형 필수(360 / 768 / 1200px), 공통 영역(navbar·footer)은 includes로 분리

InfinityFree 제약 (설계 시 반드시 반영)

서버에 SSH/Composer 없음 → 프레임워크·의존성 추가 금지(바닐라 PHP/PDO)
open_basedir로 htdocs 밖 접근 불가 → 설정 포함 모든 파일을 htdocs 안에 둔다
MySQL 뷰·스토어드 프로시저·트리거 금지 → 일반 테이블 + 쿼리(집계는 GROUP BY)
배포는 FTP 단방향(서버→GitHub 동기화 X) → 서버 파일 직접 수정 금지, GitHub이 단일 소스
이미지 최적화(자켓 WebP/JPG, 가급적 ≤200KB), 운영 display_errors=Off

디렉터리 (요약)
htdocs/ 가 웹 루트이자 FTP 업로드 대상. 페이지 .php + includes/(db,header,footer,functions) + config/(config.php는 gitignore) + assets/(css,js,img). 그 밖에 sql/(schema,seed), .github/workflows/deploy.yml, .claude/(settings,agents).
코딩·보안 원칙

모든 쿼리 PDO 프리페어드, 모든 출력 htmlspecialchars, 모든 입력 검증
DB 접속정보 config/config.php 분리(커밋 금지) + .htaccess로 직접 접근 차단

서브에이전트 역할

frontend-senior-dev (opus): HTML/CSS/JS/Bootstrap, 공통 레이아웃, 컴포넌트, 반응형, 차트 UI
php-backend-senior (opus): ※ nodejs-backend-senior에서 교체. PHP/PDO, DB 접근, 페이지 컨트롤러, 필터·집계
security-manager (opus): 프리페어드·XSS 검토, 입력 검증, config/.htaccess 보호
qa-tester (sonnet): 반응형·링크·alt·대비 감사, 페이지별 수동 테스트
cicd-manager (sonnet): GitHub Actions(php -l 린트 후 FTP로 htdocs 자동 배포)

레퍼런스

Discogs (discogs.com) — 릴리스 상세 정보 구조, 장르/포맷/연도 필터
김밥레코즈 (gimbabrecords.com) — 카드형 그리드, 음반별 큐레이션 글, 한정반 배지