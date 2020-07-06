<?php
namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use NITSAN\NsBackup\Controller\BackupBaseController;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as transalte;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility as debug;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
class BackupBaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * phpPath
     */
    public $phpPath = null;

    /**
     * rootPath
     */
    public $rootPath = null;

    /**
     * composerRootPath
     */
    public $composerRootPath = null;

    /**
     * siteUrl
     */
    public $siteUrl = null;

    /**
     * localStoragePath
     */
    public $localStoragePath = null;

    /**
     * baseURL
     */
    public $baseURL = null;

    /**
     * phpbuPath
     */
    public $phpbuPath = null;

    /**
     * arrDatabase
     */
    public $arrDatabase = array();

    /**
     * backupFileName
     */
    public $backupFileName = null;

    /**
     * backupFilePath
     */
    public $backupFilePath = null;

    /**
     * backupDownloadPath
     */
    public $backupDownloadPath = null;

    /**
     * backupFile
     */
    public $backupFile = null;

    /**
     * Inject the BackupglobalRepository repository
     *
     * @param \NITSAN\NsBackup\Domain\Repository\BackupglobalRepository $backupglobalRepository
     */
    public function injectProductRepository(BackupglobalRepository $backupglobalRepository)
    {
        $this->backupglobalRepository = $backupglobalRepository;
    }

    public function __construct()
    {
        // Initiate something
    }

    /**
     * action globalErrorValidation
     *
     * @return void
     */
    public function globalErrorValidation()
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        $errorValidation = '';
        $arrValidation = [
            'emails' => transalte::translate('global.error.emails','ns_backup'),
            'emailSubject' => transalte::translate('global.error.emailSubject','ns_backup'),
            'compress' => transalte::translate('global.error.compress','ns_backup'),
            'php' => transalte::translate('global.error.php','ns_backup'),
            'root' => transalte::translate('global.error.root','ns_backup'),
            'siteurl' => transalte::translate('global.error.siteurl','ns_backup'),
            'cleanup' => transalte::translate('global.error.cleanup','ns_backup'),
            'cleanupQuantity' => transalte::translate('global.error.cleanupQuantity','ns_backup')
        ];
        foreach ($arrValidation as $key=>$value) {
            if(empty($this->globalSettingsData[0]->$key)) {
                $errorValidation .= '<li>'.$value.'</li>';
            }
        }

        // Let's check configuration for PHPBU
        if (version_compare(phpversion(), '7.0.0') <= 0) {
            $errorValidation .= '<li>'.transalte::translate('global.error.phpversion','ns_backup').'</li>';
        }
        $arrGetLoadedExtensions = get_loaded_extensions();

        if(!in_array ('curl', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.curl','ns_backup').'</li>';
        }
        if(!in_array ('dom', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.dom','ns_backup').'</li>';
        }
        if(!in_array ('json', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.json','ns_backup').'</li>';
        }
        /* if(in_array ('spl', $arrGetLoadedExtensions)) {
            $errorValidation .= '<li>'.transalte::translate('global.error.spl','ns_backup').'</li>';
        } */

        // Let's check if exec() works
        if(!exec('echo EXEC') == 'EXEC') {
            $errorValidation .= '<li>'.transalte::translate('global.error.exec','ns_backup').'</li>';
        }

        return $errorValidation;
    }

    /**
     * action generateBackup
     *
     * @return void
     */
    public function generateBackup($arrPost)
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        // Get PHP Path
        if(!empty($this->globalSettingsData[0]->php)) {
            $this->phpPath = $this->globalSettingsData[0]->php;
        }
        else {
            $this->phpPath = exec('which php');
            if(empty($this->phpPath)) {
                $this->phpPath = 'php ';
            }
        }

        // Get TYPO3 Path
        if(!empty($this->globalSettingsData[0]->root)) {
            $this->rootPath = $this->globalSettingsData[0]->root;
        }
        else {
            $this->typo3Version = VersionNumberUtility::getNumericTypo3Version();
            if (VersionNumberUtility::convertVersionNumberToInteger($this->typo3Version) >= 9000000) {
                // If TYPO3 version is version 9 or higher
                $this->rootPath = Environment::getProjectPath();

                // Let's change root path to /public in Composer-based installation
                if(Environment::isComposerMode()) {
                    $this->rootPath = Environment::getPublicPath();
                    $this->composerRootPath = Environment::getProjectPath();
                }
            } else {
                // For TYPO3 Version 7 or lower
                $this->rootPath = PATH_site;
            }
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
        $this->phpbuPath = $this->rootPath.'/typo3conf/ext/ns_backup/phpbu.phar ';
        if (version_compare(phpversion(), '7.2.0') <= 0) {
            $this->phpbuPath = $this->rootPath.'/typo3conf/ext/ns_backup/phpbu-5.2.10.phar ';
        }

        // Get Database Configuration
        $this->arrDatabase = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];

        // Get Current Date time
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle($permitted_chars), 0, 24);
        $this->prefixFileName = date('dmY_Hi').'_'.$randomString;

        $backupName = $arrPost['backupName'];
        $backupNameOriginal = $arrPost['backupName'];
        $backupName = $this->prefixFileName.'_'.$backupName;
        $backupType = $arrPost['backupFolderSettings'];

        // Prepare backup filename
        $backupFileName = strtolower($backupName);
        $backupFileName = str_replace(' ', '_', $backupFileName);
        $backupFileName = str_replace('-', '_', $backupFileName);
        $backupFileName = preg_replace('/[^A-Za-z0-9]/', '_', $backupFileName);
        $backupFileName = preg_replace('/_+/', '_', $backupFileName);

        $jsonFolder = $this->rootPath.'/uploads/tx_nsbackup/json/';
        $jsonFile = $backupFileName.'_'.$backupType.'_configuration.json';
        $logFile = $jsonFolder.$backupFileName.'_'.$backupType.'_log.json';
        $jsonPath = $jsonFolder.$jsonFile;

        // Let's create LOG file if not existis
        if (!file_exists($logFile)) {
            $fh = @fopen($logFile, 'a');
            @fclose($fh);
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

            // Remove phpbu unnecessary lines
            //unset($log[0]);
            //unset($log[1]);
            //unset($log[2]);
            //$strLog = implode("\n", $log);

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
        }
        else {
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
                if(($backupType == 'vendor') && (strlen($this->composerRootPath) > 0)) {
                    $sourcePath = $this->composerRootPath.'/'.$targetPath;
                }

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
            }
            else if($compressTechnique == 'zip') {
                $compressTechnique = '';
            }
            else if($compressTechnique == 'gzip') {
                $compressTechnique = '.gz';
            }
            else if($compressTechnique == 'xz') {
                $compressTechnique = '.xz';
            }
            //echo $compressTechnique;exit;

            $this->backupFilePath = $this->localStoragePath.$backupType;
            $this->backupFileName = $backupFileName.$backupExtFile;

            // Physical file
            $this->backupFile = $this->backupFilePath.'/'.$backupFileName.$backupExtFile.$compressTechnique;

            // Download file
            $this->backupDownloadPath =
                $this->baseURL.
                $backupType.'/'.
                $backupFileName.$backupExtFile.$compressTechnique;

            // If Backup Type = ALL then, Let's consider mysql as special-case
            if ($backupType == 'mysqldump') {
                $this->backupFileMySQL = $this->backupFilePath . '/' . $backupFileName . $backupExtFile.$compressTechnique;
                $this->backupDownloadPathMySQL =
                    $this->baseURL .
                    $backupType . '/' .
                    $backupFileName . $backupExtFile.$compressTechnique;
            }

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
    protected function convertFilesize($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
