---
name: code-quality-baseline
description: SLEEVE codebase consistently uses h() for output escaping and PDO prepared statements for all queries — when adding code, follow the existing patterns rather than reinventing.
metadata:
  type: project
---

Across the 10 page files + `functions.php`, the codebase is already well-disciplined on the two highest-leverage controls.

**Why:** Every `$pdo->prepare(...) / execute([...])` call uses named or positional bound parameters — there is zero string interpolation into SQL. Every `<?= ... ?>` block in templates wraps output in `h()` (which is `htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8')`). Filter whitelists in `functions.php::get_albums` already restrict `format` to `['Vinyl','CD']`, and ENUM filtering keys are hardcoded.

**How to apply:**
- Do not propose wholesale SQL rewrites or sanitization wrappers — the patterns are correct. Surgical fixes only.
- When adding a new `$_GET` entry point, copy the existing whitelist patterns: regex (`curation.php` mood) or `in_array(..., true)` (`guide.php` category).
- The `LIMIT` clause in `get_related_albums` interpolates an integer after `max(1, min((int)$limit, 24))` — this is safe because the value is cast to int and clamped. Don't flag it.
- `nl2br(h($x))` is used for multi-line notes/intent/process — correct order (escape first, then add `<br>`).
- The `h()` helper is intentionally defined twice (in `functions.php` and as a `function_exists` fallback in `header.php`) so `404.php` can render even if `functions.php` fails to load.
