<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$GLOBALS['TCA']['tx_nsbackup_domain_model_backupdata']['ctrl']['hideTable'] = 1;
$GLOBALS['TCA']['tx_nsbackup_domain_model_backupglobal']['ctrl']['hideTable'] = 1;
