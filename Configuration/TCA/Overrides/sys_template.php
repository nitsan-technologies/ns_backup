<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

call_user_func(function () {

    /**
     * Add default TypoScript (constants and setup)
     */
    ExtensionManagementUtility::addStaticFile(
        'ns_backup',
        'Configuration/TypoScript',
        '[NITSAN] Backup'
    );
});
