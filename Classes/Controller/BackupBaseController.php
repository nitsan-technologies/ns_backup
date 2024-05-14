<?php
namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as transalte;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/***
 *
 * This file is part of the "Backup" Extension for TYPO3 CMS.
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
     * backupglobalRepository
     */
    protected $backupglobalRepository = null;

    /**
     * globalSettingsData
     */
    protected $globalSettingsData = null;

    /**
     * prefixFileName
     */
    protected $prefixFileName = null;

    /**
     * backupFileMySQL
     */
    protected $backupFileMySQL = null;

    /**
     * backupDownloadPathMySQL
     */
    protected $backupDownloadPathMySQL = null;

    /**
     * typo3Version
     * @var 
     */
    protected $typo3Version = null;
    public $exceptionMessage = '';

    public function __construct()
    {
        $this->exceptionMessage=transalte::translate('something.wrong.here','ns_backup');

    }
    /**
     * Inject the BackupglobalRepository repository
     *
     * @param \NITSAN\NsBackup\Domain\Repository\BackupglobalRepository $backupglobalRepository
     */
    public function injectBackupglobalRepository(BackupglobalRepository $backupglobalRepository)
    {
        $this->backupglobalRepository = $backupglobalRepository;
    }

    /**
     * globalErrorValidation
     * @return string
     */
    public function globalErrorValidation()
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        $arrKeys = ['emails', 'emailSubject', 'compress', 'php', 'root', 'siteurl', 'cleanup', 'cleanupQuantity'];
        $arrValidation = [];
        foreach ($arrKeys as $key) {
            $arrValidation[$key] = transalte::translate("global.error.$key", 'ns_backup');
        }

        $errorValidation = implode('', array_map(function ($key, $value) {
            return empty($this->globalSettingsData[0]->$key) ? '<li>' . $value . '</li>' : '';
        }, array_keys($arrValidation), $arrValidation));

        // Let's check configuration for PHPBU
        if (version_compare(phpversion(), '7.0.0') <= 0) {
            $errorValidation .= '<li>'.transalte::translate('global.error.phpversion','ns_backup').'</li>';
        }

        // Let's check configuration for PHPBU
        $arrGetLoadedExtensions = get_loaded_extensions();
        $arrExtensionsToCheck = ['curl', 'dom', 'json'];
        foreach ($arrExtensionsToCheck as $extension) {
            if (!in_array($extension, $arrGetLoadedExtensions)) {
                $errorValidation .= '<li>' . transalte::translate("global.error.$extension", 'ns_backup') . '</li>';
            }
        }
        // Check if exec() works
        if (!exec('echo EXEC') == 'EXEC') {
            $errorValidation .= '<li>' . transalte::translate('global.error.exec', 'ns_backup') . '</li>';
        }
        return $errorValidation;
    }

    /**
     *  generateBackup
     * @param mixed $arrPost
     * @return array
     */
    public function generateBackup($arrPost)
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        // Get PHP Path
        $this->phpPath = !empty($this->globalSettingsData[0]->php)
            ? $this->globalSettingsData[0]->php
            : (exec('which php') ?: 'php ');

        // Get TYPO3 Path

        if(!empty($this->globalSettingsData[0]->root)) {

            $this->rootPath = $this->globalSettingsData[0]->root;
            if(Environment::isComposerMode()) {
                $this->rootPath = Environment::getPublicPath();
                $this->composerRootPath = Environment::getProjectPath();
            }
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
                //@extensionScannerIgnoreLine
                $this->rootPath = PATH_site;
            }
        }

        // Get Local Storage Path
        $this->localStoragePath = $this->rootPath.'/uploads/tx_nsbackup/';
        try{
            if (!file_exists($this->localStoragePath)) {

                GeneralUtility::mkdir_deep($this->localStoragePath);
            }
        }catch (RuntimeException $e){
            return  [
                'log' => 'error',
                'backup_file' => $this->exceptionMessage,
            ];
        }

        // Get Base URL
        $this->siteUrl = $this->globalSettingsData[0]->siteurl ?? '';
        $this->baseURL = $this->siteUrl . '/uploads/tx_nsbackup/';

        // Get PHPHBU Path
        $this->phpbuPath = $this->rootPath.'/typo3conf/ext/ns_backup/phpbu.phar ';
        if (version_compare(phpversion(), '7.2.0') <= 0) {
            $this->phpbuPath = $this->rootPath.'/typo3conf/ext/ns_backup/phpbu-5.2.10.phar ';
        }

        // Get Database Configuration
        $this->arrDatabase = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];
        $this->arrDatabase['port'] = $this->arrDatabase['port'] ?? '3306';

        // Get Current Date time
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle($permitted_chars), 0, 24);
        $this->prefixFileName = date('dmY_Hi').'_'.$randomString;

        $backupNameOriginal = $arrPost['backupName'];
        $backupName = $this->prefixFileName.'_'.$arrPost['backupName'];
        $backupType = $arrPost['backupFolderSettings'];

        // Prepare backup filename
        $backupFileName = preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            preg_replace('/[\s-]+/', '_', strtolower(trim($backupName)))
        );

        $jsonFolder = $this->rootPath.'/uploads/tx_nsbackup/json/';
        $jsonFile = $backupFileName.'_'.$backupType.'_configuration.json';
        $logFile = $jsonFolder.$backupFileName.'_'.$backupType.'_log.json';
        $jsonPath = $jsonFolder.$jsonFile;

        // Let's create LOG file if not existis
        if (!file_exists($logFile)) {
            $fh = @fopen($logFile, 'a');
            if($fh != false){
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
            }
            $json .= $this->getPhpbuBackup($backupName, $backupType, $backupFileName);
            $json .= '
                ]
            }
        ';

        try{
            // Let's create JSCON folder does not exists
            if (!file_exists($jsonFolder)) {
                GeneralUtility::mkdir_deep($jsonFolder);
            }

            // Let's create JSON file
            file_put_contents($jsonPath, $json);

            // Prepare SSH Command
            $command = $this->phpPath. ' '. $this->phpbuPath.' --configuration='.$jsonPath.' --verbose';

            // Execute Backup SSH Command
            exec($command, $log);
        }catch (RuntimeException $e){
            return [
                'log' => 'error',
                'backup_file' => $this->backupFile,
            ];
        }


        // Validate If SSH command success
        if (count($log) > 0 && is_array($log)) {
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
            try{
                $fileSize = $this->convertFilesize(filesize($this->backupFile));
            }catch (\Exception $e){
                return  [
                    'log' => 'error',
                    'backup_file' => $this->exceptionMessage,
                ];
            }
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
        $json = '
            {
                "name": "'.$backupName.'",';

            $backupExtFile = '.tar';
        if ($backupType == 'mysqldump') {
            $json .= '
                "source": {
                    "type": "mysqldump",
                    "options": {
                        "host": "' . $this->arrDatabase['host'] . '",
                        "port": "' . $this->arrDatabase['port'] . '",
                        "databases": "' . $this->arrDatabase['dbname'] . '",
                        "user": "' . $this->arrDatabase['user'] . '",
                        "password": "' . $this->arrDatabase['password'] . '"
                    }
                },';
            $backupExtFile = '.sql';
        } else {
            $targetPath = ($backupType == 'all') ? '' : $backupType;

            // Exclude uploads/tx_nsbackup
            if ($backupType == 'uploads') {
                $ignoreUploads = ',"exclude": "tx_nsbackup"';
            }
            if ($backupType == 'all') {
                $ignoreUploads = ',"exclude": "uploads/tx_nsbackup,typo3temp"';
            }

            $sourcePath = $this->rootPath . '/' . $targetPath;

            // In composer-mode, let's figure out vendor folder
            if (($backupType == 'vendor') && $this->composerRootPath && (strlen($this->composerRootPath) > 0)) {
                $sourcePath = $this->composerRootPath . '/' . $targetPath;
            }
            $ignoreUploads = $ignoreUploads ?? '';
            $json .= '
                "source": {
                    "type": "tar",
                    "options": {
                        "path": "' . $sourcePath . '"' . $ignoreUploads . '
                    }
                },';
        }

        // PATCH If compress=bzip2
        $compressTechnique = $this->globalSettingsData[0]->compress;

        switch ($compressTechnique) {
            case 'bzip2':
            case '':
                $compressTechnique = '.bz2';
                break;
            case 'zip':
                $compressTechnique = '';
                break;
            case 'gzip':
                $compressTechnique = '.gz';
                break;
            case 'xz':
                $compressTechnique = '.xz';
                break;
            default:
                break;
        }

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
        elseif ($bytes > 1 || $bytes == 1)
        {
            $bytes = $bytes . ' bytes';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
