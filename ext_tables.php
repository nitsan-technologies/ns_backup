<?php

use TYPO3\CMS\Core\Core\Environment;

defined('TYPO3') || die('Access denied.');

// Globally configuration of PHPBU https://phpbu.de/manual/current/en/index.html

$cleanup = [
    ['key' => 'Cleanup by quantity', 'value' => 'quantity'],
];

$backuptype = [
    ['key' => 'MySQL Database', 'value' => 'mysqldump'],
    ['key' => 'Core (typo3 folder)', 'value' => 'typo3'],
    ['key' => 'Vendors (vendor folder)', 'value' => 'vendor'],
];

$compress = [
    ['key' => 'bzip2', 'value' => 'bzip2'],
    ['key' => 'zip', 'value' => 'zip'],
    ['key' => 'gzip', 'value' => 'gzip'],
    ['key' => 'xz', 'value' => 'xz'],
];

if(!Environment::isComposerMode()) {
    array_push($backuptype, ['key' => 'Extensions (typo3conf folder)', 'value' => 'typo3conf']);
}

if (!defined('cleanup')) {
    define('cleanup', $cleanup);
}

if (!defined('backuptype')) {
    define('backuptype', $backuptype);
}

if (!defined('compress')) {
    define('compress', $compress);
}
