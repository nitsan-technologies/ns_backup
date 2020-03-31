<?php
defined('TYPO3_MODE') || die('Access denied.');

// Globally configuration of PHPBU https://phpbu.de/manual/current/en/index.html

// Define Clean-up Options
define(
    "cleanup", [
        //['key' => 'Cleanup by capacity', 'value' => 'capacity'],
        //['key' => 'Cleanup by date', 'value' => 'date'],
        ['key' => 'Cleanup by quantity', 'value' => 'quantity'],
    ]);

// Define Backup Type
define(
    "backuptype", [
        ['key' => 'MySQL Database', 'value' => 'mysqldump'],
        ['key' => 'Core (typo3 folder)', 'value' => 'typo3'],
        ['key' => 'Extensions (typo3conf folder)', 'value' => 'typo3conf'],
        ['key' => 'Vendors (vendor folder)', 'value' => 'vendor'],
    ]);

// Define Compression Type
define(
    "compress", [
        ['key' => 'bzip2', 'value' => 'bzip2'],
        ['key' => 'zip', 'value' => 'zip'],
        ['key' => 'gzip', 'value' => 'gzip'],
        ['key' => 'xz', 'value' => 'xz'],
    ]);

// Configure TYPO3 Backend Module
call_user_func(
    function () {
        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'NITSAN.NsBackup',
                'tools', // Make module a submodule of 'tools'
                'backup', // Submodule key
                '', // Position
                [
                    'Backups' => 'dashboard, backuprestore, deletebackupbackup, globalsetting, premiumextension, manualbackup',
                    'Backupglobal' => 'globalsetting, create, update',
                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:ns_backup/Resources/Public/Icons/module-nsbackup.svg',
                    'labels' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_backup.xlf',
                ]
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ns_backup', 'Configuration/TypoScript', '[NITSAN] Backup');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsbackup_domain_model_backupglobal', 'EXT:ns_backup/Resources/Private/Language/locallang_csh_tx_nsbackup_domain_model_nsbackupglobal.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nsbackup_domain_model_backupglobal');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsbackup_domain_model_backupdata', 'EXT:ns_backup/Resources/Private/Language/locallang_csh_tx_nsbackup_domain_model_backupdata.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nsbackup_domain_model_backupdata');
    }
);
