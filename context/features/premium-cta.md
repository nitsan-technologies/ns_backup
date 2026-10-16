# Feature — Premium strip and ns_license free trial

*Status: Done. Requires ns_license 14.5.4 (deep-link support) to be released.*

## Licensing status

Freemium per the NITSAN Free/Freemium sheet. Premium product: https://t3planet.de/typo3-sicherung-erweiterung
(not linked from the UI; the trial stays inside the TYPO3 backend).

## Dependency

`composer.json` `"nitsan/ns-license": "^14.5.4"`, `ext_emconf.php` `'ns_license' => '14.5.4-14.9.99'`.

## Strip

- Defined as section `premiumStrip` in `Templates/Backups/Dashboard.html` only; rendered by
  `Layouts/Default.html` with `<f:render section="premiumStrip" optional="true" />` directly below the navbar,
  above flash messages and the config warning. Other views don't define the section, so nothing renders there.
- Content: `actions-rocket` core icon, headline on the first line, description below it, and one `btn btn-primary` "Start Free Trial" link with an `actions-arrow-right` icon (same tab).
- Labels in `locallang.xlf`: `premium.strip.headline` "You're using the free version of Backup Plus", `premium.strip.description` "Unlock premium features and get more out of Backup Plus.", `premium.strip.trial` "Start Free Trial". "Backup Plus" is the extension name from the Free/Freemium sheet.
- Styling: Bootstrap utilities + `.ns-backup-premium-strip` (with `__icon`, `__headline`, `__description`) in `Resources/Public/Css/main.css`. Blue highlight with rounded corners (light `#e7f2fc` / dark `#16324a`, 4px blue left border). Not a gray bar.

## Trial link

`BackupsController::dashboardAction()` builds `trialUrl` with the backend `UriBuilder`:
route `nitsan_nslicensemodule` (`NS_LICENSE_MODULE`), param `trial=ns_backup`, anchor `#free-section`
(`NS_LICENSE_FREE_SECTION_ANCHOR`).

ns_license's `get-license.js` reads `?trial=ns_backup`, clicks the existing Free Trial button for `ns_backup`
(`.t3js-get-license-trigger[data-gl-mode="trial"][data-extension-key="ns_backup"]`), which opens ns_license's
own trial modal, then strips the parameter. If ns_backup is not offered there (already licensed, not in the
catalog) the user just sees the free extension list.

Limitation: `buildUriFromRoute()` throws if the ns_license module route is missing, so the dashboard needs
ns_license installed (guaranteed by the declared dependency).
