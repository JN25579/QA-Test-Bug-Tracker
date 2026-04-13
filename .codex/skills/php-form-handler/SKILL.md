---
name: php-form-handler
description: Build or update beginner-friendly PHP forms and request handlers in this repo. Use when Codex needs to edit `php-basics/form.php`, `php-basics/process.php`, or related request parsing for `$_GET` and `$_POST`, especially for validation, sanitization, and simple result pages.
---

# PHP Form Handler

Use this skill for small form workflows in `php-basics/`.

Read these files first:
- `php-basics/form.php`
- `php-basics/process.php`
- `php-basics/helpers.php`

Follow the repo's current pattern:
- Keep the form page and processing page separate
- Use `method="post"` unless the task clearly needs query parameters
- Read request values with `isset(...)` checks before use
- Sanitize display strings with `sanitiseString()`
- Parse simple lists with `explode()`, `trim()`, and `is_numeric()` when needed

Implementation rules:
1. Add new fields in `form.php` first.
2. Handle missing or empty values explicitly in `process.php`.
3. Keep validation messages simple and visible in the HTML output.
4. Convert numeric input to `int` or `float` before calculations.
5. Prefer existing helpers over duplicating sanitizing or calculation logic.

Avoid:
- Mixing large amounts of HTML into helper functions
- Adding JavaScript unless the user asks for it
- Introducing sessions, databases, or AJAX for beginner tasks

Before finishing:
1. Confirm every form field name matches the handler code.
2. Confirm invalid input does not trigger warnings.
3. Confirm rendered user data is escaped.
