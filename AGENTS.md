# Repo Context

This repository is a small beginner PHP practice project.

## Current Structure

- `php-basics/index.php`: home page that reads simple `$_GET` values
- `php-basics/form.php`: form page
- `php-basics/process.php`: form handler for `$_POST`
- `php-basics/helpers.php`: shared helper functions
- `php-basics/nav.php`: shared navigation partial

## Working Rules

- Keep changes beginner-friendly and easy to read.
- Prefer plain PHP with simple `if`, `foreach`, variables, and small functions.
- Preserve the existing file structure unless the user asks for a refactor.
- Reuse `helpers.php` for shared logic.
- Reuse `nav.php` for shared links.
- Sanitize user-facing text before output.
- Avoid adding frameworks, Composer packages, databases, sessions, or JavaScript unless the user asks for them.

## Editing Notes

- Existing user edits may already be present in `php-basics/*.php`; do not overwrite them casually.
- Prefer small, targeted changes over broad rewrites.
- Keep HTML simple and readable.
- Match the current style of separate form and processing pages.

## Skill Location

Custom repo skills live in `.codex/skills/`.

Current skills:

- `php-basics-page-edit`
- `php-form-handler`
- `php-helpers-workflow`
