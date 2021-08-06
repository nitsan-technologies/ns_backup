<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '[NITSAN] Backup Plus',
    'description' => 'First-ever feature-rich TYPO3 backup solution with manual and scheduled backup. Easy to use and configure. Backup your TYPO3 code, assets, database etc to your favourite backup clouds/servers. Demo-Screecasts: http://demo.t3terminal.com/t3t-extensions/backup/ You can buy PRO-version to get more features and free-support at https://t3terminal.com/ns-backup-typo3-extension',
    'category' => 'module',
    'author' => 'NITSAN Technologies Pvt Ltd',
    'author_email' => 'sanjay@nitsan.in',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-10.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
