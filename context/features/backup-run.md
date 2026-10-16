# Feature — Running a backup

*Status: Done.*

## Entry point

The "Backup Now" modal posts `backuprestore[backupName]` and `backuprestore[backupFolderSettings]` to
`BackupsController::backuprestoreAction()`.

- Name must match `[0-9A-Za-z _-]`, otherwise ERROR flash + redirect.
- Runs only if `errorValidation` is empty; calls `BackupBaseController::generateBackup()`.
- On phpbu `errorCount > 0` and `emailNotificationOnError` set, sends a `MailMessage` per configured email.

## `BackupBaseController::generateBackup()`

1. Reads settings row; PHP binary = setting `php` or `which php`.
2. Paths:
   - Classic: root = setting `root` (or project path); phpbu = `<root>/typo3conf/ext/ns_backup/phpbu.phar`.
   - Composer: root = public path; composer root = `Environment::getComposerRootPath()` on TYPO3 ≤ 13,
     `Environment::getProjectPath()` on 14; phpbu = `<composer root>/vendor/nitsan/ns-backup/phpbu.phar`.
3. Storage: `<backup_store_path or root>/tx_nsbackup/<type>/`, phpbu JSON configs and logs in `.../tx_nsbackup/json/`.
4. Whitelist: `mysqldump`, `typo3`, `vendor`, `typo3conf`; anything else throws `RuntimeException`.
5. Builds a phpbu JSON config (json + mail logging, one backup entry, `tar` source or `mysqldump` with
   `TYPO3_CONF_VARS[DB][Connections][Default]`), compression from settings (`bzip2`/`gzip`/`xz`),
   cleanup `quantity` with `cleanupQuantity`.
6. Executes `php phpbu.phar --configuration=<json> --verbose` via `exec()` (args escaped).
7. Inserts a `tx_nsbackup_domain_model_backupdata` row; `download_url` only when the store path is public
   (`isPathPublic()`).

Backup file name on disk: `md5(<type>)-YmdHi.<tar|sql>.<compression ext>`.
