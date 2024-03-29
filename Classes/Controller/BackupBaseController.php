<?php

namespace NITSAN\NsBackup\Controller;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as transalte;

/***
 *
 * This file is part of the "[NITSAN] Backup" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019
 *
 ***/

/**
 * BackupBaseController
 */
class BackupBaseController extends ActionController
{
    /**
     * phpPath
     *
     * @var string
     */
    public $phpPath = null;

    /**
     * rootPath
     *
     * @var string
     */
    public $rootPath = null;

    /**
     * composerRootPath
     *
     * @var string
     */
    public $composerRootPath = null;

    /**
     * siteUrl
     *
     * @var string
     */
    public $siteUrl = null;

    /**
     * localStoragePath
     *
     * @var string
     */
    public $localStoragePath = null;

    /**
     * baseURL
     *
     * @var string
     */
    public $baseURL = null;

    /**
     * phpbuPath
     *
     * @var string
     */
    public $phpbuPath = null;

    /**
     * arrDatabase
     *
     * @var array
     */
    public $arrDatabase = array();

    /**
     * backupFileName
     *
     * @var string
     */
    public $backupFileName = null;

    /**
     * backupFilePath
     *
     * @var string
     */
    public $backupFilePath = null;

    /**
     * backupDownloadPath
     *
     * @var string
     */
    public $backupDownloadPath = null;

    /**
     * backupFile
     *
     * @var string
     */
    public $backupFile = null;

    /**
     * globalSettingsData
     *
     * @var array
     */
    protected $globalSettingsData;

    /**
     * prefixFileName
     *
     * @var string
     */
    protected $prefixFileName;

    /**
     * backupFileMySQL
     */
    protected $backupFileMySQL;


    /**
     * backupDownloadPathMySQL
     */
    protected $backupDownloadPathMySQL;

    /**
     * backupglobalRepository
     */
    protected $backupglobalRepository;

    /**
     * __construct
     * @param BackupglobalRepository $backupglobalRepository
     */
    public function __construct(
        BackupglobalRepository $backupglobalRepository
    ) {
        $this->backupglobalRepository = $backupglobalRepository;
    }

    /**
     * action globalErrorValidation
     *
     * @return string
     */
    public function globalErrorValidation()
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        $errorValidation = '';
        $arrValidation = [
            'emails' => transalte::translate('global.error.emails', 'ns_backup'),
            'emailSubject' => transalte::translate('global.error.emailSubject', 'ns_backup'),
            'compress' => transalte::translate('global.error.compress', 'ns_backup'),
            'php' => transalte::translate('global.error.php', 'ns_backup'),
            'root' => transalte::translate('global.error.root', 'ns_backup'),
            'siteurl' => transalte::translate('global.error.siteurl', 'ns_backup'),
            'cleanup' => transalte::translate('global.error.cleanup', 'ns_backup'),
            'cleanupQuantity' => transalte::translate('global.error.cleanupQuantity', 'ns_backup')
        ];
        foreach ($arrValidation as $key => $value) {
            if(empty($this->globalSettingsData[0]->$key)) {
                $errorValidation .= '<li>'.$value.'</li>';
            }
        }

        // Let's check configuration for PHPBU
        $arrGetLoadedExtensions = get_loaded_extensions();

        if(!in_array('curl', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.curl', 'ns_backup').'</li>';
        }
        if(!in_array('dom', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.dom', 'ns_backup').'</li>';
        }
        if(!in_array('json', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.json', 'ns_backup').'</li>';
        }
        // Let's check if exec() works
        if(!exec('echo EXEC') == 'EXEC') {
            $errorValidation .= '<li>'.transalte::translate('global.error.exec', 'ns_backup').'</li>';
        }

        return $errorValidation;
    }

    /**
     * action generateBackup
     *
     * @return array
     */
    public function generateBackup($arrPost)
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        // Get PHP Path
        if(!empty($this->globalSettingsData[0]->php)) {
            $this->phpPath = $this->globalSettingsData[0]->php;
        } else {
            $this->phpPath = exec('which php');
            if(empty($this->phpPath)) {
                $this->phpPath = 'php ';
            }
        }

        // Get TYPO3 Path
        if(!empty($this->globalSettingsData[0]->root)) {
            $this->rootPath = $this->globalSettingsData[0]->root;
        } else {
            // If TYPO3 version is version 9 or higher
            $this->rootPath = Environment::getProjectPath();
        }

        // Let's change root path to /public in Composer-based installation
        if(Environment::isComposerMode()) {
            $this->rootPath = Environment::getPublicPath();
            $this->composerRootPath = Environment::getComposerRootPath();
        }

        // Get Local Storage Path
        $this->localStoragePath = $this->rootPath.'/uploads/tx_nsbackup/';
        if (!file_exists($this->localStoragePath)) {
            mkdir($this->localStoragePath, 0775, true);
        }

        // Get Base URL
        $this->siteUrl = '';
        if(!empty($this->globalSettingsData[0]->siteurl)) {
            $this->siteUrl = $this->globalSettingsData[0]->siteurl;
        }
        $this->baseURL = $this->siteUrl.'/uploads/tx_nsbackup/';

        // Get PHPHBU Path
        if(Environment::isComposerMode()) {
            $this->phpbuPath = $this->composerRootPath.'/vendor/nitsan/ns-backup/phpbu.phar';
        } else {
            $this->phpbuPath = $this->rootPath.'/typo3conf/ext/ns_backup/phpbu.phar';
        }

        // Get Database Configuration
        $this->arrDatabase = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];
        if(empty($this->arrDatabase['port'])) {
            $this->arrDatabase['port'] = '3306';
        }

        // Get Current Date time
        $this->prefixFileName = date('dmY_Hi');

        $backupName = $arrPost['backupName'];
        $backupNameOriginal = $arrPost['backupName'];
        $backupName = $this->prefixFileName.'_'.$backupName;
        $backupType = $arrPost['backupFolderSettings'];

        // Prepare backup filename
        $backupFileName = strtolower(trim($backupName));
        $backupFileName = str_replace(' ', '_', $backupFileName);
        $backupFileName = str_replace('-', '_', $backupFileName);
        $backupFileName = preg_replace('/[^A-Za-z0-9]/', '_', $backupFileName);
        $backupFileName = preg_replace('/_+/', '_', $backupFileName);

        $jsonFolder = $this->rootPath.'/uploads/tx_nsbackup/json/';
        $jsonFile = GeneralUtility::trimExplode('_', $backupFileName, true, 3)[2] . '_' . $backupType . '_configuration.json';
        $logFile = $jsonFolder . GeneralUtility::trimExplode('_', $backupFileName, true, 3)[2] . '_' . $backupType . '_log.json';
        $jsonPath = $jsonFolder.$jsonFile;

        // Let's create LOG file if not existis
        if (!file_exists($logFile)) {
            $fh = @fopen($logFile, 'a');
            if($fh != false) {
                @fclose($fh);
            }
        }

        $json = '
            {
                "verbose": true,
                "debug": false,
                "logging": [
                    {
                        "type": "json",
                        "target": "'.$logFile.'"
                    },
                    {
                        "type": "mail",
                        "options": {
                        "transport": "mail",
                        "recipients": "'.$this->globalSettingsData[0]->emails.'",
                        "subject": "['.$backupType.'] '.$backupNameOriginal. ' - '.$this->globalSettingsData[0]->emailSubject.'",
                        "sendOnlyOnError": "'.$this->globalSettingsData[0]->emailNotificationOnError.'"
                        }
                    }
                ],
                "backups": [';

        // Let's check if admin wants "Backup Everyting"
        if ($backupType == 'all') {

            // Create Database Backup
            $json .= $this->getPhpbuBackup($backupName, 'mysqldump', $backupFileName). ',';

            // Create Code Backup
            $json .= $this->getPhpbuBackup($backupName, $backupType, $backupFileName);
        } else {
            // Create Specific Selected Type of Backup
            $json .= $this->getPhpbuBackup($backupName, $backupType, $backupFileName);
        }

        $json .= '
                ]
            }
        ';

        // Let's create JSCON folder does not exists
        if (!file_exists($jsonFolder)) {
            mkdir($jsonFolder);
        }

        // Let's create JSON file
        file_put_contents($jsonPath, $json);

        // Prepare SSH Command
        $command = $this->phpPath. ' '. $this->phpbuPath.' --configuration='.$jsonPath.' --verbose';

        // Execute Backup SSH Command
        exec($command, $log, $return_var);

        // Validate If SSH command success
        if (count($log) > 0) {
            $log = file_get_contents($logFile);

            // Get ready to insert to Backup History
            $arrPost['jsonfile'] = $jsonFile;

            // If Backup Everything, Then let's first-insert MySQL as special case
            if ($backupType == 'all') {
                $arrPost['backup_type'] = 'mysqldump';
                $arrPost['download_url'] = $this->backupDownloadPathMySQL;

                $fileSize = $this->convertFilesize(filesize($this->backupFileMySQL));
                $arrPost['size'] = $fileSize;

                $arrPost['filenames'] = $this->backupFileMySQL;

                $this->backupglobalRepository->addBackupData($arrPost);
            }

            // Insert to Database > Backup History
            $arrPost['download_url'] = $this->backupDownloadPath;
            $arrPost['log'] = $log;

            $fileSize = $this->convertFilesize(filesize($this->backupFile));
            $arrPost['size'] = $fileSize;
            $arrPost['filenames'] = $this->backupFile;

            $this->backupglobalRepository->addBackupData($arrPost);

            $arrReturn = [
                'log' => $log,
                'backup_file' => $this->backupFile,
                'download_url' => $this->backupDownloadPath,
            ];
        } else {
            $arrReturn = [
                'log' => 'error',
                'backup_file' => $this->backupFile,
            ];
        }

        return $arrReturn;
    }

    /**
      * Generate PHP BU action getPhpbuBackupJSON
      *
      */
    protected function getPhpbuBackup($backupName, $backupType, $backupFileName)
    {
        $json = '';
        $json .= '
            {
                "name": "'.$backupName.'",';

        $backupExtFile = '.tar';
        switch($backupType) {
            case 'mysqldump':
                $json .= '
                "source": {
                    "type": "mysqldump",
                    "options": {
                        "host": "'.$this->arrDatabase['host'].'",
                        "port": "'.$this->arrDatabase['port'].'",
                        "databases": "'.$this->arrDatabase['dbname'].'",
                        "user": "'.$this->arrDatabase['user'].'",
                        "password": "'.$this->arrDatabase['password'].'"
                    }
                },';
                $backupExtFile = '.sql';
                break;

            default:
                $targetPath = ($backupType == 'all') ? '' : $backupType;

                // Exclude uploads/tx_nsbackup
                if($backupType == 'uploads') {
                    $ignoreUploads = ',"exclude": "tx_nsbackup"';
                }
                if($backupType == 'all') {
                    $ignoreUploads = ',"exclude": "uploads/tx_nsbackup,typo3temp"';
                }

                $sourcePath = $this->rootPath.'/'.$targetPath;

                // In composer-mode, let's figure out vendor folder
                if(($backupType == 'vendor') && $this->composerRootPath) {
                    if((strlen($this->composerRootPath) > 0)) {
                        $sourcePath = $this->composerRootPath.'/'.$targetPath;
                    }
                }
                $ignoreUploads = isset($ignoreUploads) ? $ignoreUploads : '';
                $json .= '
                "source": {
                    "type": "tar",
                    "options": {
                        "path": "'.$sourcePath.'"'.$ignoreUploads.'
                    }
                },';
        }

        // PATCH If compress=bzip2
        $compressTechnique = $this->globalSettingsData[0]->compress;
        if($compressTechnique == 'bzip2' || empty($compressTechnique)) {
            $compressTechnique = '.bz2';
        } elseif($compressTechnique == 'zip') {
            $compressTechnique = '';
        } elseif($compressTechnique == 'gzip') {
            $compressTechnique = '.gz';
        } elseif($compressTechnique == 'xz') {
            $compressTechnique = '.xz';
        }
        //echo $compressTechnique;exit;

        $this->backupFilePath = $this->localStoragePath.$backupType;
        $this->backupFileName = $backupFileName.$backupExtFile;
        // Physical file
        $this->backupFile = $this->backupFilePath . '/' . $backupType.'-'.date('Ymd-Hi') . $backupExtFile . $compressTechnique;

        // Download file
        $this->backupDownloadPath =
            $this->baseURL .
            $backupType . '/' .
            $backupType.'-'.date('Ymd-Hi') . $backupExtFile . $compressTechnique;

        // If Backup Type = ALL then, Let's consider mysql as special-case
        if ($backupType == 'mysqldump') {
            if($this->globalSettingsData[0]->compress == 'zip'){
                $compressTechnique = '';
            }
            $this->backupFileMySQL = $this->backupFilePath . '/' . 'mysqldump' .'-'.date('Ymd-Hi') . $backupExtFile . $compressTechnique;
            $this->backupDownloadPathMySQL =
                $this->baseURL . $this->backupFileMySQL =
                $backupType . '/' .
                $backupFileName . $backupExtFile . $compressTechnique;
        }
        $this->backupFileName =  $backupType.'-%Y%m%d-%H%i' . $backupExtFile;

        $json .= '
                "target": {
                    "dirname": "'.$this->backupFilePath.'",
                    "filename": "'.$this->backupFileName.'",
                    "compress": "'.$this->globalSettingsData[0]->compress.'"
                },';

        $json .= '
                "cleanup": {
                    "type": "'.$this->globalSettingsData[0]->cleanup.'",
                    "options": {
                        "amount": "'.$this->globalSettingsData[0]->cleanupQuantity.'"
                    }
                }
            }
        ';
        return $json;
    }

    /**
     * Convert File Size
     */
    protected function convertFilesize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
