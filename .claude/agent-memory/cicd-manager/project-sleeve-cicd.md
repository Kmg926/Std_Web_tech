---
name: project-sleeve-cicd
description: CI/CD setup for the SLEEVE vinyl archive PHP project deployed to InfinityFree via FTP
metadata:
  type: project
---

SLEEVE is a PHP/MySQL vinyl archive + jacket design portfolio site hosted on InfinityFree.

**Pipeline design adopted:**
- Single workflow file: `.github/workflows/deploy.yml`
- Trigger: push to `main` only (+ `workflow_dispatch` for manual runs)
- Job 1 `lint`: PHP syntax check with `php -l` on all `htdocs/*.php`, PHP 8.1 via `shivammathur/setup-php@v2`
- Job 2 `deploy`: `SamKirkland/FTP-Deploy-Action@4.3.4`, only runs if lint passes (`needs: lint`)
- FTP source: `./htdocs/` → server dest: `/htdocs/`

**Secrets required (exact names):**
- `FTP_SERVER` — InfinityFree FTP host (e.g. `ftpupload.net`)
- `FTP_USERNAME`
- `FTP_PASSWORD`

**Excluded from FTP upload:** `.git*`, `.git*/**`, `**/config.php`

**Key constraints:**
- InfinityFree: FTP only, no SSH access
- `htdocs/config/config.php` is git-ignored and must be configured manually on the server
- Web root on InfinityFree is `/htdocs/`

**Why:** InfinityFree is shared hosting with FTP-only access; SamKirkland/FTP-Deploy-Action was chosen for reliability with this host.

**How to apply:** When modifying the deploy pipeline, preserve the `needs: lint` gate and the exact secret names above.
