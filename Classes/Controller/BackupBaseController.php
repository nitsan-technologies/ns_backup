<?php
namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as transalte;

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
    public $arrDatabase = [];

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
    protected $typo3Version  = null;
    public $exceptionMessage = '';

    public function __construct()
    {
        $this->exceptionMessage = transalte::translate('something.wrong.here', 'ns_backup');
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

        $arrKeys       = ['emails', 'emailSubject', 'compress', 'php', 'root', 'siteurl', 'cleanup', 'cleanupQuantity'];
        $arrValidation = [];
        foreach ($arrKeys as $key) {
            $arrValidation[$key] = transalte::translate("global.error.$key", 'ns_backup');
        }

        $errorValidation = implode('', array_map(function ($key, $value) {
            return empty($this->globalSettingsData[0]->$key) ? '<li>' . $value . '</li>' : '';
        }, array_keys($arrValidation), $arrValidation));

        // Let's check configuration for PHPBU
        $arrGetLoadedExtensions = get_loaded_extensions();
        $arrExtensionsToCheck   = ['curl', 'dom', 'json'];
        foreach ($arrExtensionsToCheck as $extension) {
            if (! in_array($extension, $arrGetLoadedExtensions)) {
                $errorValidation .= '<li>' . transalte::translate("global.error.$extension", 'ns_backup') . '</li>';
            }
        }

        // Check if exec() works
        if (! exec('echo EXEC') == 'EXEC') {
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
        $this->phpPath = ! empty($this->globalSettingsData[0]->php)
            ? $this->globalSettingsData[0]->php
            : (exec('which php') ?: 'php ');

        // Get TYPO3 Path - Keep original v9/v10 logic
        if (! empty($this->globalSettingsData[0]->root)) {
            $this->rootPath = $this->globalSettingsData[0]->root;
            if (Environment::isComposerMode()) {
                $this->rootPath         = Environment::getPublicPath();
                $this->composerRootPath = Environment::getProjectPath();
                $this->phpbuPath        = $this->composerRootPath . '/vendor/nitsan/ns-backup/phpbu.phar';
            }
        } else {
            $this->typo3Version = VersionNumberUtility::getNumericTypo3Version();
            if (VersionNumberUtility::convertVersionNumberToInteger($this->typo3Version) >= 9000000) {
                $this->rootPath = Environment::getProjectPath();
                // Let's change root path to /public in Composer-based installation
                if (Environment::isComposerMode()) {
                    $this->rootPath         = Environment::getPublicPath();
                    $this->composerRootPath = Environment::getProjectPath();
                }
            } else {
                // For TYPO3 Version 7 or lower
                //@extensionScannerIgnoreLine
                $this->rootPath = PATH_site;
            }
        }

        // Get Local Storage Path - FIXED: Use consistent paths
        $globalBackupStorePath = $this->globalSettingsData[0]->getBackupStorePath();
        $isPublicPath          = $this->isPathPublic($globalBackupStorePath);

        if ($globalBackupStorePath == '') {
            $this->localStoragePath = $this->rootPath . '/uploads/tx_nsbackup/'; // Use uploads directory
            $jsonFolder             = $this->rootPath . '/uploads/tx_nsbackup/json/';
        } else {
            $this->localStoragePath = $globalBackupStorePath . '/tx_nsbackup/';
            $jsonFolder             = $globalBackupStorePath . '/tx_nsbackup/json/';
        }

        try {
            if (! file_exists($this->localStoragePath)) {
                GeneralUtility::mkdir_deep($this->localStoragePath);
            }
            // Ensure json folder exists
            if (! file_exists($jsonFolder)) {
                GeneralUtility::mkdir_deep($jsonFolder);
            }
        } catch (RuntimeException $e) {
            return [
                'log'         => 'error',
                'backup_file' => $this->exceptionMessage,
            ];
        }

        // Get Base URL - FIXED: Use working logic from original version
        $this->siteUrl = $this->globalSettingsData[0]->siteurl ?? '';

        // Use the working baseURL logic
        if ($globalBackupStorePath == '') {
            $this->baseURL = $this->siteUrl . '/uploads/tx_nsbackup/';
        } else {
            // For custom paths, construct URL properly
            if ($isPublicPath) {
                $relativePath  = str_replace(Environment::getPublicPath(), '', $this->localStoragePath);
                $this->baseURL = $this->siteUrl . rtrim($relativePath, '/') . '/';
            } else {
                $this->baseURL = ''; // Private path, no direct download
            }
        }

        // Get PHPHBU Path - Keep original logic
        $this->phpbuPath = $this->rootPath . '/typo3conf/ext/ns_backup/phpbu.phar';
        if (version_compare(phpversion(), '7.2.0') <= 0) {
            $this->phpbuPath = $this->rootPath . '/typo3conf/ext/ns_backup/phpbu-5.2.10.phar';
        }

        // Get Database Configuration
        $this->arrDatabase         = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];
        $this->arrDatabase['port'] = $this->arrDatabase['port'] ?? '3306';

        // Get Current Date time - Use working filename generation
        $permitted_chars      = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString         = substr(str_shuffle($permitted_chars), 0, 24);
        $this->prefixFileName = date('dmY_Hi') . '_' . $randomString;

        $backupNameOriginal = $arrPost['backupName'];
        $backupName         = $this->prefixFileName . '_' . $arrPost['backupName'];
        $currentDateTime    = '';

        // Prepare backup filename
        $backupFileName = preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            preg_replace('/[\s-]+/', '_', strtolower(trim($backupName)))
        );

        // Whitelist allowed backup types - Added 'other' type from v12/v13
        $allowedBackupTypes = ['mysqldump', 'typo3', 'vendor', 'typo3conf', 'other'];
        $backupType         = $arrPost['backupFolderSettings'] ?? '';

        if (! in_array($backupType, $allowedBackupTypes, true)) {
            throw new RuntimeException('Invalid backup type specified.');
        }

        // Generate random string for file names - From v12/v13
        $fileRandomString = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $backupBaseName   = GeneralUtility::trimExplode('_', $backupFileName, true, 3)[1];

        $jsonFile = $backupBaseName . '_' . $fileRandomString . '_' . $backupType . '_configuration.json';
        $logFile  = $jsonFolder . $backupBaseName . '_' . $fileRandomString . '_' . $backupType . '_log.json';
        $jsonPath = $jsonFolder . $jsonFile;

        // Let's create LOG file if not exists
        if (! file_exists($logFile)) {
            $fh = @fopen($logFile, 'a');
            if ($fh != false) {
                @fclose($fh);
            }
        }

        // Email configuration - From v12/v13 with security improvements
        $emailString = $this->globalSettingsData[0]->emails ?? '';
        $emailArray  = array_map('trim', explode(',', $emailString));
        $validEmails = array_filter($emailArray, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        $emailRecipients          = implode(',', $validEmails);
        $emailSubject             = preg_replace('/[^A-Za-z0-9_\-\[\]\s]/', '', $this->globalSettingsData[0]->emailSubject ?? '');
        $emailNotificationOnError = $this->globalSettingsData[0]->emailNotificationOnError === '1' ? '1' : '0';

        // Prepare JSON configuration using json_encode for security - From v12/v13
        $jsonConfig = [
            'verbose'         => true,
            'debug'           => false,
            'logging'         => [
                [
                    'type'   => 'json',
                    'target' => $logFile,
                ],
                [
                    'type'    => 'mail',
                    'options' => [
                        'transport'  => 'mail',
                        'recipients' => $emailRecipients,
                        'subject'    => "[{$backupType}] {$backupNameOriginal} - {$emailSubject}",
                        'sendOnlyOnError' => $emailNotificationOnError,
                    ],
                ],
            ],
            'backups' => [],
        ];

        // Let's check if admin wants "Backup Everything"
        if ($backupType == 'all') {
            // store date and time before backup - From v12/v13
            $currentDateTime = date('Ymd-Hi');
            // Create Database Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, 'mysqldump', $backupFileName);
            // Create Code Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName);
        } elseif ($backupType == 'other') {
            // New functionality from v12/v13 - custom path backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName, $arrPost['custompath']);
        } else {
            // Create Specific Selected Type of Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName);
        }

        $json = json_encode($jsonConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        try {
            // Write JSON content to file
            file_put_contents($jsonPath, $json);

            // Validate and sanitize PHP path - Security improvement from v12/v13
            if (! is_string($this->phpPath) || ! file_exists($this->phpPath) || ! is_executable($this->phpPath)) {
                throw new RuntimeException("Invalid PHP executable path.");
            }

            // Prepare secure shell command - Security improvement from v12/v13
            $phpBin    = escapeshellcmd($this->phpPath);
            $phpbuBin  = escapeshellarg($this->phpbuPath);
            $configArg = escapeshellarg('--configuration=' . $jsonPath);

            $command = "$phpBin $phpbuBin $configArg --verbose";

            // Execute Backup SSH Command
            exec($command, $log, $return_var);
        } catch (RuntimeException $e) {
            return [
                'log'         => 'error',
                'backup_file' => 'Something is wrong here.' . $e->getMessage(),
            ];
        }

        // Validate If SSH command success
        if (count($log) > 0 && is_array($log)) {
            $log = file_get_contents($logFile);

            // Get ready to insert to Backup History
            $arrPost['jsonfile'] = $jsonFile;

            // If Backup Everything, Then let's first-insert MySQL as special case
            if ($backupType == 'all') {
                $arrPost['backup_type']  = 'mysqldump';
                $arrPost['download_url'] = '';
                if ($isPublicPath) {
                    $arrPost['download_url'] = $this->backupDownloadPathMySQL;
                }

                // File size calculation
                try {
                    $fileSize = $this->convertFilesize(filesize($this->backupFileMySQL));
                } catch (\Exception $e) {
                    $fileSize = '0 bytes';
                }
                $arrPost['size']      = $fileSize;
                $arrPost['filenames'] = $this->backupFileMySQL;
                $this->backupglobalRepository->addBackupData($arrPost);
            }

            // Insert to Database > Backup History
            $arrPost['download_url'] = '';
            if ($isPublicPath) {
                $arrPost['download_url'] = $this->backupDownloadPath;
            }
            $arrPost['log'] = $log;

            try {
                $fileSize = $this->convertFilesize(filesize($this->backupFile));
            } catch (\Exception $e) {
                $fileSize = '0 bytes';
            }

            $arrPost['size']      = $fileSize;
            $arrPost['filenames'] = $this->backupFile;

            $downloadURL = '';
            if ($isPublicPath) {
                $downloadURL = $this->backupDownloadPath;
            }
            $this->backupglobalRepository->addBackupData($arrPost);
            $arrReturn = [
                'log'          => $log,
                'backup_file'  => $this->backupFile,
                'download_url' => $downloadURL,
            ];

            // Add message only if path is private
            if (! $isPublicPath) {
                $arrReturn['message'] = transalte::translate('backup.private.path.message', 'ns_backup');
            }
        } else {
            $arrReturn = [
                'log'         => 'error',
                'backup_file' => $this->backupFile,
            ];
        }
        return $arrReturn;
    }

    /**
     * Generate PHP BU backup configuration as array - NEW from v12/v13
     * @param string $backupName
     * @param string $backupType
     * @param string $backupFileName
     * @param string|null $rawName
     * @return array
     */
    protected function getPhpbuBackupArray(string $backupName, string $backupType, string $backupFileName, ?string $rawName = null): array
    {
        $ignoreUploads = '';
        $backupConfig  = [
            'name' => $backupName,
        ];

        $backupExtFile = '.tar';
        switch ($backupType) {
            case 'mysqldump':
                $backupConfig['source'] = [
                    'type'    => 'mysqldump',
                    'options' => [
                        'host'      => $this->arrDatabase['host'],
                        'port'      => (string) $this->arrDatabase['port'],
                        'databases' => $this->arrDatabase['dbname'],
                        'user'      => $this->arrDatabase['user'],
                        'password'  => $this->arrDatabase['password'],
                    ],
                ];
                $backupExtFile = '.sql';
                break;

            default:
                $targetPath = ($backupType == 'all') ? '' : $backupType;

                // Exclude uploads/tx_nsbackup
                if ($backupType == 'uploads') {
                    $ignoreUploads = 'tx_nsbackup';
                }
                if ($backupType == 'all') {
                    $ignoreUploads = 'uploads/tx_nsbackup,typo3temp';
                }

                $sourcePath = $this->rootPath . '/' . $targetPath;

                // In composer-mode, let's figure out vendor folder
                if (($backupType == 'vendor') && ($this->composerRootPath !== null && strlen($this->composerRootPath) > 0)) {
                    $sourcePath = $this->composerRootPath . '/' . $targetPath;
                }

                $sourceOptions = [
                    'path' => ($backupType == 'other') ? $rawName : $sourcePath,
                ];

                if (! empty($ignoreUploads)) {
                    $sourceOptions['exclude'] = $ignoreUploads;
                }

                $backupConfig['source'] = [
                    'type'    => 'tar',
                    'options' => $sourceOptions,
                ];
        }

        $compressTechnique  = $this->globalSettingsData[0]->compress;
        $compressTechniques = [
            'bzip2' => '.bz2',
            'zip'   => '',
            'gzip'  => '.gz',
            'xz'    => '.xz',
        ];

        $compressExt = $compressTechniques[$compressTechnique] ?? '.bz2';

        // Ensure backup type directory exists
        $this->backupFilePath = $this->localStoragePath . $backupType;
        if (! file_exists($this->backupFilePath)) {
            GeneralUtility::mkdir_deep($this->backupFilePath);
        }
        $fileTimestamp    = date('Ymd-Hi');
        $this->backupFile = $this->backupFilePath . '/' . md5($backupType) . '-' . $fileTimestamp . $backupExtFile . $compressExt;

        $this->backupDownloadPath =
        $this->baseURL .
        $backupType . '/' .
        md5($backupType) . '-' . $fileTimestamp . $backupExtFile . $compressExt;

        // If Backup Type = ALL then, Let's consider mysql as special-case
        if ($backupType == 'mysqldump') {
            if ($this->globalSettingsData[0]->compress == 'zip') {
                $compressExt = '';
            }
            $this->backupFileMySQL         = $this->backupFilePath . '/' . md5($backupType) . '-' . $fileTimestamp . $backupExtFile . $compressExt;
            $this->backupDownloadPathMySQL =
            $this->baseURL .
            $backupType . '/' .
            md5($backupType) . '-' . $fileTimestamp . $backupExtFile . $compressExt;
        }

        $this->backupFileName = md5($backupType) . '-%Y%m%d-%H%i' . $backupExtFile;

        $backupConfig['target'] = [
            'dirname'  => $this->backupFilePath,
            'filename' => $this->backupFileName,
            'compress' => $this->globalSettingsData[0]->compress,
        ];

        $backupConfig['cleanup'] = [
            'type'    => $this->globalSettingsData[0]->cleanup,
            'options' => [
                'amount' => $this->globalSettingsData[0]->cleanupQuantity,
            ],
        ];

        return $backupConfig;
    }

    /**
     * Convert File Size - Updated with v12/v13 improvements
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

    /**
     * @param string $path
     * @return boolean
     */
    public function isPathPublic(string $path): bool
    {
        if (! Environment::isComposerMode()) {
            $valuesToCheck = ['typo3', 'typo3conf', 'vendor', 'typo3temp', 'bin'];
            $parts         = array_filter(explode('/', rtrim($path, '/')));
            return empty(array_intersect($parts, $valuesToCheck));
        }
        return str_contains(rtrim($path, '/'), Environment::getPublicPath());
    }
}
