<?php

$EM_CONF['ns_backup'] = [
    'title' => '[NITSAN] Backup Plus',
    'description' => 'First-ever feature-rich TYPO3 backup solution with manual and scheduled backup. Easy to use and configure. Backup your TYPO3 code, assets, database etc to your favourite backup clouds/servers. Demo-Screecasts: http://demo.t3planet.com/t3t-extensions/backup/ You can buy PRO-version to get more features and free-support at https://t3planet.com/ns-backup-typo3-extension',
    'category' => 'module',
    'author' => 'Team NITSAN',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
