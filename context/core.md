# ns_backup — Core facts

## Identity

| Item | Value |
|---|---|
| Extension key | `ns_backup` |
| Composer name | `nitsan/ns-backup` |
| Title | Backup Plus |
| Namespace | `NITSAN\NsBackup\` → `Classes/` |
| Version (ext_emconf) | 14.0.2 |
| Repository | https://github.com/nitsan-technologies/ns_backup |
| Licensing status (NITSAN Free/Freemium sheet) | **Freemium** — this repo is the free version |
| Premium version (sheet) | https://t3planet.de/typo3-sicherung-erweiterung (€199/year) |

## Compatibility

| Source | TYPO3 | PHP |
|---|---|---|
| `ext_emconf.php` | `12.0.0-14.9.99` | not declared |
| `composer.json` | `^12 \|\| ^13 \|\| ^14` | not declared |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — |

- Effective minimum is **TYPO3 12.4.18** because of ns_license; the extension's own constraint still says 12.0.0.
- Code uses constructor promotion, `mixed`, union types and `str_contains()` → PHP 8.1+ (TYPO3 12 minimum anyway).

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core` | `^12 \|\| ^13 \|\| ^14` | core |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | TYPO3 Shop module + free-trial deep link (see `features/premium-cta.md`) |
| `phpbu.phar` (bundled, phpbu 6.0.5) | — | runs the actual backups via `exec()` |

Server requirements checked at runtime: PHP extensions `curl`, `dom`, `json`, and a working `exec()`
(`BackupBaseController::globalErrorValidation()`).

## Tooling

- No PHPUnit, PHPStan, CS fixer or package.json in the repo.
- CI: `.github/workflows/ter-release.yml` only (publishes tags to TER via tailor).
- Translations: Crowdin (`.crowdin.yml`), source files `Resources/Private/Language/*.xlf`.

## Conventions

- Extbase backend module, `ModuleTemplateFactory` + `renderResponse()`.
- Labels from `locallang.xlf` via `<f:translate>` / `LocalizationUtility` (aliased `transalte` in `BackupsController`).
- CSS in `Resources/Public/Css/main.css`, uses `light-dark()` for dark mode; Bootstrap 5.3 is vendored and loaded by the layout.
- JS modules via `Configuration/JavaScriptModules.php` (`@nitsan/ns-backup/`), jQuery-based.

## Known issues (existing, not fixed)

- `Configuration/Backend/Modules.php` lists `globalsetting, manualbackup` for `BackupsController`, but those actions don't exist there.
- `Configuration/TypoScript/*` (module view paths) is not registered anywhere and points to non-existent `Resources/Private/Backend/*` paths; templates resolve via Extbase defaults.
- `deletebackupbackupAction()` takes `uid` from the query string and ends with `die;`.
- `composer.json` licence `GPL-2.0+` is a deprecated SPDX id (`composer validate` warning).
- README badges/links are outdated (v13.0.2, `t3planet.com/typo3-backup-extension` vs. sheet premium URL).
- Several dead branches in `generateBackup()` (`all`, `other`, `uploads`) are unreachable because of the type whitelist.
