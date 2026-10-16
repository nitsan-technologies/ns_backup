# AGENTS.md — ns_backup (Backup Plus)

Agent router for the Backup Plus free extension. Load only what your task needs.

> **Principles:** `context/principles.md`
> **Session log:** `context/session-state.md`
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | Guardrails, "nothing that works may break" rule |
| `context/core.md` | Identity, TYPO3/PHP/ns_license constraints, tooling, known issues |

---

## Task router

| Task | Load |
|---|---|
| Module registration, layout, navigation, dashboard, assets/JS | `context/features/backend-module.md` |
| Running a backup (`generateBackup()`, phpbu, file paths) | `context/features/backup-run.md` |
| Backup history list, download, logs, delete | `context/features/backup-history.md` |
| Global Settings form + requirement checks | `context/features/global-settings.md` |
| Premium strip / free trial / ns_license dependency | `context/features/premium-cta.md` |
| Classes, tables, templates, routes | `context/architecture.md` |
| Lint / validate / manual test | `tasks/run-quality.md` |
| Update agent context after a change | `tasks/context-update.md` |

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| Admin backend module (Dashboard, Backup History, Global Settings) | Done | `context/features/backend-module.md` |
| Manual backup via bundled phpbu (DB, `typo3`, `vendor`, `typo3conf`) | Done | `context/features/backup-run.md` |
| Backup history (download, logs, delete) | Done | `context/features/backup-history.md` |
| Global Settings + server requirement check | Done | `context/features/global-settings.md` |
| Premium strip + ns_license free-trial deep link | Done (needs ns_license 14.5.4 release) | `context/features/premium-cta.md` |

---

## Quick commands

```bash
cd packages/ns_backup            # or typo3conf/ext/ns_backup in Classic mode
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done
composer validate --no-check-publish
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```
