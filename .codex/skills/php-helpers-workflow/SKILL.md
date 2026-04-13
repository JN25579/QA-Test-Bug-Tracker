---
name: php-helpers-workflow
description: Extend or clean up simple helper logic in this repo. Use when Codex needs to add, reuse, or adjust functions in `php-basics/helpers.php`, or when page code in `php-basics/index.php` and `php-basics/process.php` should be simplified by moving repeated logic into helper functions.
---

# PHP Helpers Workflow

Use this skill when logic belongs in `php-basics/helpers.php`.

Read these files first:
- `php-basics/helpers.php`
- The page that calls the helper, usually `php-basics/index.php` or `php-basics/process.php`

Prefer helpers for:
- Sanitizing repeated user input handling
- Simple numeric calculations
- Small boolean checks such as age or status rules
- Parsing reusable value transformations

Keep helpers beginner-friendly:
- One clear job per function
- Typed parameters and return types when already practical
- Straightforward `if`, `foreach`, and `return` statements
- Names that describe behaviour directly

Do not move logic into helpers if it is:
- Purely presentational HTML
- Used only once and clearer inline
- Complex enough that the repo would need a bigger refactor

When adding a helper:
1. Put it in `helpers.php`.
2. Replace duplicated page logic with a call to the helper.
3. Keep the calling page readable for a beginner learning PHP.

Before finishing:
1. Check the helper name matches its behaviour.
2. Check the new helper is actually used.
3. Check pages still `require 'helpers.php';` before calling it.
