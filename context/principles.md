# ns_backup — Operating Principles

*Every change to code, context or docs must serve the goals below.*

---

## Goal 1 — Nothing that works may break

The extension is declared for TYPO3 v12, v13 and v14 at the same time (Composer and Classic mode).

- Every change must keep all three majors working. Follow the existing version switch
  (`VersionNumberUtility` in `BackupBaseController::generateBackup()`) instead of inventing a new one.
- Prefer the smallest change that fixes the root cause. No drive-by refactors of the backup flow.
- Run `tasks/run-quality.md` and re-test the affected flow in a backend before claiming done.

## Goal 2 — Token-cost efficiency (agent context)

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load on demand:** read `context/features/<area>.md` for the task, not everything.
- **Prune as you go:** keep `context/session-state.md` short.

## Goal 3 — Accuracy and trust

- Answer from context + code, never from invented API, table or field names.
- Conflicting facts across files = fix the source file.

---

## Guardrails (non-negotiable)

- Never log or echo DB credentials: `getPhpbuBackupArray()` writes them into the phpbu JSON config.
- Keep the backup-type whitelist in `generateBackup()`; it is the only guard before `exec()`.
- Shell arguments go through `escapeshellcmd()` / `escapeshellarg()`; keep it that way.
- Do not edit `phpbu.phar` by hand.
- Do not change ns_license from an ns_backup task; reuse its module and trial modal.
- All UI text goes into `Resources/Private/Language/locallang.xlf`, no hard-coded labels.

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature or bugfix (update the matching `context/features/*.md` + `session-state.md`)
