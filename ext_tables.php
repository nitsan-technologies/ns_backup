<?php
defined('TYPO3_MODE') || die('Access denied.');

// Define Clean-up Options
define(
    "cleanup", [
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
            $backupController = 'Backups';
            $backupglobalController = 'Backupglobal';
            if (version_compare(TYPO3_branch, '10.0', '>=')) {
                $backupController = \NITSAN\NsBackup\Controller\BackupsController::class;
                $backupglobalController = \NITSAN\NsBackup\Controller\BackupglobalController::class;
            }
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'NITSAN.NsBackup',
                'tools', // Make module a submodule of 'tools'
                'backup', // Submodule key
                '', // Position
                [
                    $backupController => 'dashboard, backuprestore, deletebackupbackup, globalsetting, manualbackup',
                    $backupglobalController => 'globalsetting, create, update',
                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:ns_backup/Resources/Public/Icons/module-nsbackup.svg',
                    'labels' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_backup.xlf',
                ]
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ns_backup', 'Configuration/TypoScript', 'Backup');
    }
);
