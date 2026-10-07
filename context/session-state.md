# ns_backup — Session state

## 2026-10-07 — ns_license integration + premium strip

- Branch `feature/ns-license-premium-strip` (from `master` @ 9f26850).
- Added `nitsan/ns-license ^14.5.4` / `ns_license 14.5.4-14.9.99`.
- Dashboard premium strip with "Start Free Trial" → ns_license module `?trial=ns_backup#free-section` (see `features/premium-cta.md`).
- Created agent context (`AGENTS.md`, `CLAUDE.md`, `context/`, `tasks/`); no previous context existed.
- Verified on TYPO3 14 (v14compo, CLI render): trial URL resolves to `/typo3/module/nitsan/NsLicense?...&trial=ns_backup#free-section`, all templates parse, strip renders. TYPO3 12/13 checked statically only.
- Open: ns_license 14.5.4 not yet published (latest 14.5.3); click-through in a real browser session still pending.
