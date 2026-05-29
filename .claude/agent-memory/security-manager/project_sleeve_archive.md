---
name: project-sleeve-archive
description: SLEEVE is a read-only vinyl/design archive — no auth, forms, file uploads, or state-changing endpoints. Threat model is GET-only.
metadata:
  type: project
---

SLEEVE is a public, read-only PHP/MySQL archive site (vinyl collection + jacket-design portfolio). It has no login, no comments, no forms, no file uploads, no admin panel — every page is a GET handler that reads from MySQL and renders HTML.

**Why:** The owner is the sole content author and writes records directly into the database. There is no user-generated input path.

**How to apply:**
- CSRF/session/auth findings are not applicable — do not flag missing CSRF tokens or session hardening unless the user adds a form.
- Focus the threat model on: reflected XSS via `$_GET`, stored XSS via DB-supplied strings, SQL injection through `$_GET` slugs/categories/moods, info disclosure via PHP errors, and direct access to `config/`.
- The 10 entry points are: index, about, collection, album (?slug), designs, design (?slug), curation (?mood), guide (?category), stats, 404.
- Helper `h()` is defined in both `functions.php` and `header.php` (idempotent via `function_exists`). All output should go through it.
