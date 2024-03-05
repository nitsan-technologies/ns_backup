<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal',
        'label' => 'emails',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'emails,email_notification_on_error,email_notification_on_success,default_server,password_restore,backup_validation,encryption,cleanup_local_name,cleanup_local_value,cleanup_server_name,cleanup_server_value,quick_setup_wizard,cleanup,compress,php,root,siteurl',
        'iconfile' => 'EXT:ns_backup/Resources/Public/Icons/tx_nsbackup_domain_model_backupglobal.gif',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_diffsource, hidden, emails, email_notification_on_error, email_notification_on_success, default_server, password_restore, backup_validation, encryption, cleanup_local_name, cleanup_local_value, cleanup_server_name, cleanup_server_value, quick_setup_wizard, cleanup, compress,php,root,siteurl, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    'label' => '1',
                    'value' => [
                        'label' => 0,
                        'value' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

        'emails' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.emails',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email_subject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.email_subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email_notification_on_error' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.email_notification_on_error',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ]
        ],
        'email_notification_on_success' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.email_notification_on_success',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ]
        ],
        'default_server' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.default_server',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'password_restore' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.password_restore',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'backup_validation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.backup_validation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'encryption' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.encryption',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cleanup_local_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanup_local_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cleanup_local_value' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanup_local_value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cleanup_server_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanup_server_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cleanup_server_value' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanup_server_value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'quick_setup_wizard' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.quick_setup_wizard',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ]
        ],
        'cleanup' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanup',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cleanup_quantity' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.cleanupquantity',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ],
        ],
        'compress' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.compress',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'php' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.php',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'root' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.root',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'siteurl' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupglobal.siteurl',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
    ],
];
