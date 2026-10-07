# Feature — Backup history

*Status: Done.*

`BackupsController::backuprestoreAction()` (menu "Backup History") lists all rows of
`tx_nsbackup_domain_model_backupdata` (newest first) in a DataTables table.

- Logs: JSON log is pretty-printed into a hidden `#logDiv_<uid>` and copied into the `#logBackup` modal.
- Download: no `download_url` (private path) → `#backupDownloadError` modal; URL set but `get_headers()`
  fails/404 → `#backupNotAvailable` modal; otherwise a direct link (`target="_blank"`).
- Delete: `#nsBackupDeletebackupModal` → AJAX GET to `deletebackupbackupAction` with `uid`; removes the DB row,
  the backup file, and the JSON config/log files, then `die`s. Page reloads on success.
