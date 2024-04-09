<?php

$EM_CONF['ns_backup'] = [
    'title' => '[NITSAN] Backup Plus',
    'description' => 'Easily backup your entire TYPO3 website. The Backup Plus extension for TYPO3 lets you save your code, files, and database with just a few clicks. Install Backup Plus and connect it to your cloud storage (like Google Drive, Dropbox, Amazon S3, SFTP, Rsync, etc.) and backup your entire TYPO3 site manually with a single click.

*** Live Demo: https://demo.t3planet.com/t3-extensions/backup *** Premium Version, Documentation & Free Support: https://t3planet.com/typo3-backup-extension',
    'category' => 'module',
    'author' => 'T3D: Rohan Parmar, Divya Goklani, Nilesh Malankiya, QA: Gautam Kunjadiya',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'version' => '12.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
