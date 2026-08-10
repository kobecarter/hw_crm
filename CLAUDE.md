# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

"Hello World CRM" — a PHP/MySQL CRM running an ad/marketing agency's full back office (clients, devis, factures, charges, fournisseurs, Moroccan tax compliance, HR, bank reconciliation) across multiple agencies (HW Label, Verse Concept, etc.) from one install. No framework — a hand-rolled MVC convention repeated identically across ~30 `com_*` components.

## Commands

There is no build step, package manager script, or test suite — this is classic unbundled PHP served directly by Apache (XAMPP).

- **Run it**: XAMPP's Apache/MySQL (already running via the XAMPP control panel), served at `http://localhost/hw_crm/`.
- **PHP CLI**: there is no `php`/`php-cgi` on `PATH`. Always use the XAMPP-bundled binaries with an absolute path:
  ```
  /Applications/XAMPP/xamppfiles/bin/php-cgi -l path/to/file.php      # lint
  /Applications/XAMPP/xamppfiles/bin/php-cgi -f path/to/script.php    # execute a standalone script
  ```
  A standalone script that `require`s `config.php`/`instanceDb.php` must be run with the shell's cwd set to the project root (`cd` there first) — those files use relative paths and silently fail to resolve otherwise.
- **MySQL CLI**:
  ```
  /Applications/XAMPP/xamppfiles/bin/mysql -uroot keha1057_crm -e "..."
  ```
  Add `--default-character-set=utf8` when the query touches accented (French) text, or output mangles.
- **Composer deps** (`vendor/`, already installed): mpdf (PDF generation), phpmailer, phpoffice/phpspreadsheet, smalot/pdfparser, setasign/fpdi, firebase/php-jwt.
- **No automated tests exist.** Verify changes by linting (`php-cgi -l`) every changed file and, for anything UI-facing, actually driving the page (Playwright is the established approach here — see below).

## Architecture

### Component structure (repeated identically ~30 times)

Every feature lives under `components/com_X/`:
- `index.php` — the page-request task router. Reads `$_GET['task']`, sets up view data (`$clients = client::findAll(...)`, etc.), `include_once`s a view.
- `controleurs/router.php` — the **AJAX** entry point (`components/com_X/controleurs/router.php?task=...`). Has its own bootstrap (`require config.php`, `instanceDb.php`, `session_start()`), independent of `index.php`. Dispatches to `controleurs/X/controleur.php` task functions, gated by `hasDroit('add'|'edit'|'delete'|'view', 'com_X')`.
- `classes/X.php` — the model. No ORM; hand-written SQL via `sprintf()` + `GetSQLValueString()` for escaping.
- `views/X/{list,form}.php` — templates, plain PHP+HTML, jQuery/AJAX for interactivity (no frontend framework/bundler).

**Autoloading**: `instanceDb.php`'s `my_autoloader()` scans *every* `components/com_*/classes/` directory for a file matching the requested class name and `require_once`s it. This means any class is globally available with zero registration — but also that class filenames must be unique across the entire `components/` tree, and a typo'd/missing class silently fails to autoload rather than erroring clearly at the call site.

### The `$db->query()` always-falsy gotcha (critical, easy to get backwards)

`components/com_config/classes/mysql.php::query()` never `return`s anything — it only ever returns falsy, on success *and* failure alike. The entire codebase's convention accounts for this by never trusting the return value for correctness, only for the falsy/truthy branch shape:
```php
if (!$db->query($SQL)) {
    return 1; // "success" — always taken, because query() is always falsy
} else {
    return 2; // effectively dead code
}
```
Every model's `add()`/`edit()`/`delete()` follows this exact shape, and callers check `->add() == 1` for success. **New model methods must copy this shape** even though it looks inverted — "fixing" it would be a much larger, separate refactor and would break the `== 1` convention everywhere else. `queryS($sql)` (note the `S`) is different: it actually executes and returns the result set as an associative array, used for `SELECT`s expected to return rows.

### Two separate request pipelines per page

A page load (`index.php?option=com_X`) and its AJAX actions (`components/com_X/controleurs/router.php?task=...`) bootstrap independently — session/db setup is duplicated across every `controleurs/router.php`, not shared with `index.php`. When adding a new mutating action, it needs a `case` in `controleurs/router.php` (with a `hasDroit()` check) *and* a task function in `controleurs/X/controleur.php`; the view's JS posts to the router path, not to `index.php`.

### Multi-agency session model

`$_SESSION['agence']` scopes almost every query. Most `X::findAll()`/`X::find()` methods take an `$agence` parameter and join through to filter by it. Switching agency happens via `com_config/controleurs/router.php?task=switchAgence`. `ensureSessionAgence(targetAgenceId, callback)` (`assets/js/ia-client-modal.js`) is the reusable JS helper for "switch agency only if needed, then proceed" flows (used by cross-agency search results, IA-driven client creation, etc.).

Some agencies pool bank accounts/detection together (`$groupeMaroc = array(1, 3, 25)` — hardcoded agency IDs, appears in a few places like `com_rapprochement`'s bank auto-detection) rather than being purely per-agency.

### Lazy cron pattern

There's no real server crontab. Scheduled-seeming behavior (payment reminders, TVA deadline Slack alerts, job-offer Slack polling, rappel expiration emails) is implemented as a public `controleurs/router.php` task gated by `hash_equals($SECRET, $_GET['secret'])`, meant to be pinged periodically by an external service (cron-job.org). These endpoints call `bootstrapSystemSession($actingUserId, $agenceId, $langue)` (`includes/functions/functions.php`) to fake a session (most model code assumes `$_SESSION['user']`/`$_SESSION['agence']` exist). The acting user must be a superuser (`su=1`) or agency-scoped queries silently under-return. Secrets live in `config.secrets.php` (gitignored) alongside all other API keys (Anthropic, Slack, Trello, Google service account, SMTP).

### AI integration (`com_ia`)

Not a page of its own — a capability embedded into other components' forms (devis PDF-import, client presentation-import, service description rewriting, TVA declaration reading, RH document pre-fill, bank-statement PDF reading). `aiExtractor` (`components/com_ia/classes/aiExtractor.php`) wraps the Anthropic API calls; extraction endpoints return structured JSON that calling components map onto their own model setters. When a flow needs to hand off extracted data across a redirect (e.g. AI devis-chat → devis form), the established mechanism is writing to `sessionStorage` under fixed keys (`ia_services`, `ia_conditions`, `ia_client_pays`) that the destination form already polls on load — prefer reusing that channel over inventing a new one.

### Frontend conventions

- jQuery + Select2 (`.chosen-select`/`.select` classes) for dropdowns, including free-entry multi-select (`tags: true`).
- GSAP for entrance animation: always `stagger: { amount: X, from: 'start' }` (a fixed time budget divided across items), never a fixed per-item delay — a fixed delay causes multi-second cascades on lists with 100+ items.
- `.glass-page` class (`assets/css/modern-theme.css`) gives the frosted-glass card look used on redesigned pages.
- Confirmation/doublon popups use the `.tva-confirm-modal` + `.tva-confirm-icon`/`.charge-doublon-icon` pattern (centered icon bubble in a color gradient, centered title) rather than the browser's native `confirm()` — this is the established visual language for "are you sure" and "duplicate detected" moments across the app.

### The `api/` directory

A separate PSR-4 (`App\`) mini-app under `api/`, with its own composer autoload, own PHPMailer/JWT vendor copies, and the Leaf PHP framework. This is a distinct subsystem (external/mobile-facing API) from the main `com_*` CRM front end — don't assume conventions from one apply to the other.

## Working environment notes

- Real production database (`keha1057_crm`) — there is no seeded test/staging copy. Any exploratory `INSERT`/`UPDATE` used to verify a feature must be deleted afterward, and if a test touches an existing row (rather than a freshly-inserted one), capture its exact prior state first so it can be restored.
- `config.secrets.php` and `.claude/` are gitignored; nothing else is — uploaded client documents under `images/` are tracked in git as-is (pre-existing convention, not something introduced later).
