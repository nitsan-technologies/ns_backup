<?php

$backupController = \NITSAN\NsBackup\Controller\BackupsController::class;
$backupglobalController = \NITSAN\NsBackup\Controller\BackupglobalController::class;

return [
    'nitsan_nsbackup' => [
        'parent' => 'tools',
        'position' => ['before' => 'top'],
        'access' => 'user,group',
        'path' => '/module/nitsan/NsBackupBackup    ',
        'icon' => 'EXT:ns_backup/Resources/Public/Icons/module-nsbackup.svg',
        'labels' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_backup.xlf',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'extensionName' => 'NsBackup',
        'controllerActions' => [
            $backupController => 'dashboard, backuprestore, deletebackupbackup, globalsetting, manualbackup',
            $backupglobalController => 'globalsetting, create, update',
        ],
    ],
];
