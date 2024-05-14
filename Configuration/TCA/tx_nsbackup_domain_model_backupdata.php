<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata',
        'label' => 'title',
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
        'searchFields' => 'title,start_datetime,end_datetime,backup_type,download_url,filenames,size,logs,date,status',
        'iconfile' => 'EXT:ns_backup/Resources/Public/Icons/tx_nsbackup_domain_model_backupdata.gif',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_diffsource, hidden, title, start_datetime, end_datetime, backup_type, download_url, filenames, size, logs, date, status, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    [
                        'label' => '',
                        'value' => 0
                    ],
                ],
                'foreign_table' => 'tx_nsbackup_domain_model_backupdata',
                'foreign_table_where' => 'AND tx_nsbackup_domain_model_backupdata.pid=###CURRENT_PID### AND tx_nsbackup_domain_model_backupdata.sys_language_uid IN (-1,0)',
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
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'start_datetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.start_datetime',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'end_datetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.end_datetime',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'backup_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.backup_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'download_url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.download_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'filenames' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.filenames',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'size' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.size',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'logs' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.logs',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.date',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_backup/Resources/Private/Language/locallang_db.xlf:tx_nsbackup_domain_model_backupdata.status',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

    ],
];
