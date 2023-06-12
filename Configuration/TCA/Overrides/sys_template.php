<?php

defined('TYPO3') || die();

call_user_func(function () {

    /**
     * Add default TypoScript (constants and setup)
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'ns_backup',
        'Configuration/TypoScript',
        '[NITSAN] Backup'
    );
});
