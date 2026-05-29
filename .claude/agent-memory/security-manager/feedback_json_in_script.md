---
name: feedback-json-in-script
description: When embedding json_encode output inside an inline <script> block, always add JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP — htmlspecialchars does not apply in script context.
metadata:
  type: feedback
---

`json_encode()` output dropped into a `<script>...</script>` block must include the four HEX flags: `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP` (combine with `JSON_UNESCAPED_UNICODE` for Korean content).

**Why:** Inside `<script>`, the HTML parser is looking for `</script>` — a DB-supplied string like `</script><svg onload=alert(1)>` will break the script context and execute. `h()` / `htmlspecialchars` is not used here because the data must remain valid JSON; the HEX flags escape `<`, `>`, `'`, `"`, and `&` as `\uXXXX` sequences which are valid JSON *and* cannot terminate the script tag. This was discovered in [[project-sleeve-archive]] when reviewing `stats.php` where genre/year keys come straight from the `albums` table.

**How to apply:**
- Always add the four HEX flags when emitting JSON into inline `<script>`.
- Not needed when the JSON is fetched via `XHR`/`fetch` and parsed as `application/json` — only the inline-in-HTML case.
- In SLEEVE, the canonical flag set is defined as `$json_script_flags` near the top of `stats.php` — reuse the same pattern in any future page that does the same thing.
