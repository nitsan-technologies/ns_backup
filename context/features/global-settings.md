# Feature — Global Settings and requirement checks

*Status: Done.*

## Form

`BackupglobalController::globalsettingAction()` renders `Partials/GlobalSetting/Globalform.html`; the form posts
to `create` (no row yet) or `update`.

Fields (`Backupglobal` properties): `emails` (comma list), `emailFrom`, `emailSubject`, `compress`, `cleanup`,
`cleanupQuantity` (1–500, JS), `backupStorePath`, `php`, `root`, `siteurl`. `varPath` is passed for the store-path hint.

Server-side validation in `create`/`update`: every email valid, `backupStorePath` is an existing directory,
`php` path is executable. Client-side validation in `Main.js` (`.btn-global-submit`).

## Requirement checks (`BackupBaseController::globalErrorValidation()`)

Returns an HTML `<li>` list for: empty `emails`, `emailSubject`, `compress`, `php`, `root`, `siteurl`, `cleanup`,
`cleanupQuantity`; missing PHP extensions `curl`, `dom`, `json`; `exec()` not working. Shown in the layout's
warning box; blocks manual backups while non-empty.
