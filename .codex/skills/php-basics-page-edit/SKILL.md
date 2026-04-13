---
name: php-basics-page-edit
description: Make safe beginner-friendly edits to simple PHP pages in this repo. Use when Codex needs to change content or behaviour in `php-basics/index.php`, `php-basics/form.php`, or shared partials like `php-basics/nav.php` without breaking the existing include/require structure.
---

# PHP Basics Page Edit

Work inside the existing `php-basics/` layout.

Read the target page first:
- `php-basics/index.php` for query-string driven output
- `php-basics/form.php` for form markup
- `php-basics/nav.php` for shared navigation

Keep the current beginner-friendly style:
- Preserve `require 'helpers.php';` and `include 'nav.php';`
- Prefer small variables, `if` blocks, and direct `echo` output
- Avoid introducing classes, frameworks, dependency managers, or advanced abstractions
- Preserve plain HTML structure unless the task requires layout changes

For page changes:
1. Find where request data is read and where output is rendered.
2. Sanitize user-facing text with `sanitiseString()` before rendering.
3. Keep control flow obvious and local to the page.
4. Reuse `nav.php` for navigation changes instead of duplicating links.

Before finishing:
1. Check that links still point to existing files.
2. Check that every opened PHP block is closed correctly.
3. Check that user input is escaped before HTML output.
