# Run quality checks

The repo has no PHPUnit, PHPStan or CS config. Available checks:

```bash
# PHP syntax, once per installed PHP version (8.1+)
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done

# composer.json (expected warning: deprecated licence id "GPL-2.0+")
composer validate --no-check-publish

# XLF well-formedness
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```

## Manual backend test (per TYPO3 major 12 / 13 / 14)

1. Admin Tools → Backup Plus → Dashboard: premium strip shows above flash messages; "Start Free Trial"
   opens the ns_license module and its trial modal for `ns_backup`.
2. Backup History and Global Settings show no strip.
3. Save Global Settings, run a "MySQL Database" backup, download/log/delete it from Backup History.
4. Check light and dark backend themes.
