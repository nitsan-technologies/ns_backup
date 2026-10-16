# ns_backup — Architecture

## Backend module

`Configuration/Backend/Modules.php` registers one module:

| Key | Parent | Access | Path |
|---|---|---|---|
| `nitsan_nsbackup` | `tools` (Admin Tools, positioned at top) | `admin` | `/module/nitsan/NsBackupBackup` |

Extbase controller/actions: `BackupsController` → `dashboard` (default), `backuprestore`, `deletebackupbackup`;
`BackupglobalController` → `globalsetting`, `create`, `update`.

No frontend plugin, no TCA forms in use (both tables `hideTable = 1` via `Configuration/TCA/Overrides/sys_template.php`),
no hooks, events, middleware or scheduler tasks.

## Classes

| Class | Role |
|---|---|
| `Controller/BackupsController` | Dashboard, Backup History (+ runs manual backup on POST), delete; builds ns_license trial URL |
| `Controller/BackupglobalController` | Global Settings form, create/update with validation |
| `Controller/BackupBaseController` | Not routed; injected service with `globalErrorValidation()`, `generateBackup()`, phpbu config builder |
| `Domain/Model/Backupglobal` | Global settings entity (public typed properties) |
| `Domain/Model/BackupData` | History entity (not used by controllers; history uses raw arrays) |
| `Domain/Repository/BackupglobalRepository` | Extbase repo for settings + QueryBuilder/Connection helpers for `tx_nsbackup_domain_model_backupdata` |

DI: `Configuration/Services.yaml` autowires `NITSAN\NsBackup\` (models excluded).

## Tables (`ext_tables.sql`)

| Table | Content |
|---|---|
| `tx_nsbackup_domain_model_backupglobal` | Single settings row: `emails`, `email_subject`, `email_from`, `email_notification_on_error`, `cleanup`, `cleanup_quantity`, `compress`, `php`, `root`, `siteurl`, `backup_store_path` |
| `tx_nsbackup_domain_model_backupdata` | One row per backup: `title`, `start_datetime`, `backup_type`, `download_url`, `filenames`, `jsonfile`, `size`, `logs` (`end_datetime`, `date`, `status` unused) |

## Global constants (`ext_tables.php`)

`cleanup`, `backuptype`, `compress` are PHP constants (arrays) defined in `ext_tables.php` and read in
controllers with `constant()`. `typo3conf` is added to `backuptype` only in Classic mode; `zip` compression is commented out.

## Views

| File | Used by |
|---|---|
| `Layouts/Default.html` | All views: CSS includes, navigation, optional `premiumStrip` section, flash messages, config warnings, server-time footer, all modals (backup now, logs, delete, download errors) |
| `Templates/Backups/Dashboard.html` | Dashboard; defines `premiumStrip` section |
| `Templates/Backups/Backuprestore.html` | Backup History |
| `Templates/Backupglobal/Globalsetting.html` + `Partials/GlobalSetting/Globalform.html` | Global Settings |

## Assets

- CSS (loaded via `<link>` in layout): `Vendor/bootstrap5.3.min.css`, `Css/Datatables.css`, `Css/main.css`, `Css/highlight/default.css`, `Css/font-awesome.css`.
- JS (`loadJavaScriptModule` in each action): `@nitsan/ns-backup/jquery.js` (jQuery 3.7.1), `@nitsan/ns-backup/Main.js` (imports `Datatables.js`).
- Unused leftovers in `Resources/Public/Vendor/` (jQuery 2.1.4, popper, matchHeight, main.js) and `JavaScript/Highlight.js`.

## Labels

`locallang.xlf` (UI), `locallang_backup.xlf` (module labels), `locallang_db.xlf` and `locallang_csh_*.xlf` (TCA), `locallang_nsbackup.xlf`.
