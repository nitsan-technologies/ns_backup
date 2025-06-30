<?php

$EM_CONF['ns_backup'] = [
    'title' => 'TYPO3 Backup Plus',
    'description' => 'Easily back up your entire TYPO3 site—including code, files, and database—with one click. Supports cloud storage like Google Drive, Dropbox, Amazon S3, SFTP, Rsync, and more.',

    'category' => 'module',
    'author' => 'Team T3Planet',
    'author_email' => 'info@t3planet.de',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'version' => '13.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-13.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'classmap' => ['Classes/']
    ]
];
