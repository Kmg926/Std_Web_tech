---
name: infinityfree-constraints
description: InfinityFree hosting blocks php.ini edits and some .htaccess directives — push hardening into PHP itself.
metadata:
  type: project
---

InfinityFree (the project's host) imposes constraints that change where security configuration must live.

**Why:** Shared free hosting — no shell access, no php.ini overrides, restrictive `open_basedir`, and some Apache modules may be unavailable.

**How to apply:**
- `display_errors`, `display_startup_errors`, `log_errors` cannot be set globally. Set them via `ini_set()` at the top of `htdocs/includes/db.php` — it is the first file every page requires, so it runs before any code that could emit a PHP error.
- `open_basedir` confines includes to `htdocs/`, so `config/config.php` lives **inside** the web root and must be blocked via `.htaccess` (FilesMatch + RewriteRule on `^config/`). Do not suggest moving config above the web root — it won't work here.
- Wrap header directives in `<IfModule mod_headers.c>` and rewrite directives in `<IfModule mod_rewrite.c>` so missing modules don't 500 the site.
- Avoid `php_flag` / `php_value` outside `<IfModule mod_php.c>` — InfinityFree often runs PHP via FastCGI where those directives 500.
