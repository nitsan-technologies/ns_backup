# Feature — Backend module, layout, dashboard

*Status: Done.*

## Layout (`Layouts/Default.html`)

Top to bottom: extension name + navbar (Dashboard, Backup History, Global Settings), optional
`premiumStrip` section, `<f:flashMessages />`, config warning box (`errorValidation`, sanitized HTML),
`content` section, server-time callout, modals.

The `modalAttr` variable (`data-bs-`) is assigned by every action and prefixes Bootstrap data attributes.

## Dashboard (`BackupsController::dashboardAction()`)

- Assigns `backupglobal`, last 5 backups (`findBackupDataAll(5)`), `backuptype`, `cleanup`, `compress`,
  `errorValidation`, `trialUrl`.
- Cards: "Backup Logs" (last 5 entries + link to Backup History) and "Backup Now!" (opens `#backupNowModal`).
- Premium strip at the top: see `premium-cta.md`.

## Config warning

`BackupsController::initializeView()` calls `globalErrorValidation()` (see `global-settings.md`) and adds an
ERROR flash message when anything is missing; the detailed list is shown in the layout's warning box.

## Main.js

Opens modals (Bootstrap API with jQuery fallback), validates the backup name and Global Settings form,
dims the page while a backup runs, deletes history rows via AJAX to the `deletebackupbackup` action,
copies log HTML into the log modal, initialises DataTables on `.ns-backup-datatable`, and shows
`#yoursiteUrlMsg` when the configured site URL differs from `window.location.origin`.
