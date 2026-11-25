<?php

namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
class BackupBaseController extends ActionController
{
    /**
     * phpPath
     *
     * @var string
     */
    public string $phpPath = '';

    /**
     * rootPath
     *
     * @var string
     */
    public string $rootPath = '';

    /**
     * composerRootPath
     *
     * @var string
     */
    public string $composerRootPath = '';

    /**
     * siteUrl
     *
     * @var string
     */
    public string $siteUrl = '';

    /**
     * localStoragePath
     *
     * @var string
     */
    public string $localStoragePath = '';

    /**
     * baseURL
     *
     * @var string
     */
    public string $baseURL = '';

    /**
     * phpbuPath
     *
     * @var string
     */
    public string $phpbuPath = '';

    /**
     * arrDatabase
     *
     * @var array
     */
    public array $arrDatabase = [];

    /**
     * backupFileName
     *
     * @var string
     */
    public string $backupFileName = '';

    /**
     * backupFilePath
     *
     * @var string
     */
    public string $backupFilePath = '';

    /**
     * backupDownloadPath
     *
     * @var string
     */
    public string $backupDownloadPath = '';

    /**
     * backupFile
     *
     * @var string
     */
    public string $backupFile = '';

    /**
     * globalSettingsData
     *
     * @var object
     */
    protected object $globalSettingsData;

    /**
     * prefixFileName
     *
     * @var string
     */
    protected string $prefixFileName;

    /**
     * backupFileMySQL
     */
    protected mixed $backupFileMySQL;


    /**
     * backupDownloadPathMySQL
     */
    protected $backupDownloadPathMySQL;

    /**
     * Unpredictable token used in physical backup filenames.
     */
    protected string $backupFileToken = '';

    public string $exceptionMessage = '';

    /**
     * __construct
     * @param BackupglobalRepository $backupglobalRepository
     */
    public function __construct(
        protected BackupglobalRepository $backupglobalRepository
    ) {
        $this->exceptionMessage = LocalizationUtility::translate('something.wrong.here', 'ns_backup');

    }

    /**
     * Default non-public backup store root (TYPO3 var path).
     */
    public function getDefaultBackupStorePath(): string
    {
        return rtrim(Environment::getVarPath(), '/');
    }

    /**
     * Resolve storage + json folders from settings. Empty store path falls back to var path.
     *
     * @return array{storePath: string, localStoragePath: string, jsonFolder: string}
     */
    public function resolveBackupStoragePaths(?object $globalSettings = null): array
    {
        if ($globalSettings === null) {
            $all = $this->backupglobalRepository->findAll();
            $globalSettings = $all[0] ?? null;
        }

        $configured = '';
        if ($globalSettings !== null && method_exists($globalSettings, 'getBackupStorePath')) {
            $configured = trim((string)$globalSettings->getBackupStorePath());
        } elseif ($globalSettings !== null && isset($globalSettings->backupStorePath)) {
            $configured = trim((string)$globalSettings->backupStorePath);
        }

        $storePath = $configured !== '' ? rtrim($configured, '/') : $this->getDefaultBackupStorePath();

        return [
            'storePath' => $storePath,
            'localStoragePath' => $storePath . '/tx_nsbackup/',
            'jsonFolder' => $storePath . '/tx_nsbackup/json/',
        ];
    }

    /**
     * True when $filePath resolves inside $allowedRoot (no path traversal).
     */
    public function isPathInsideDirectory(string $filePath, string $allowedRoot): bool
    {
        if ($filePath === '' || $allowedRoot === '') {
            return false;
        }

        $realFile = realpath($filePath);
        $realRoot = realpath($allowedRoot);
        if ($realFile === false || $realRoot === false) {
            // File may not exist yet; compare normalized absolute paths.
            $normalizedFile = PathUtility::getCanonicalPath($filePath);
            $normalizedRoot = rtrim(PathUtility::getCanonicalPath($allowedRoot), '/');
            return str_starts_with($normalizedFile, $normalizedRoot . '/')
                || $normalizedFile === $normalizedRoot;
        }

        $realRoot = rtrim($realRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return str_starts_with($realFile, $realRoot) || $realFile === rtrim($realRoot, DIRECTORY_SEPARATOR);
    }

    /**
     * Deny HTTP access to backup folders on Apache when store is (accidentally) web-reachable.
     */
    public function ensureBackupDirectoryProtection(string $directory): void
    {
        if ($directory === '' || !is_dir($directory)) {
            return;
        }

        $htaccess = rtrim($directory, '/') . '/.htaccess';
        if (!file_exists($htaccess)) {
            @file_put_contents(
                $htaccess,
                "# ns_backup: deny direct HTTP access\n"
                . "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n"
                . "<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"
            );
        }

        $webConfig = rtrim($directory, '/') . '/web.config';
        if (!file_exists($webConfig)) {
            @file_put_contents(
                $webConfig,
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
                . "<configuration><system.webServer><security><authorization>"
                . "<remove users=\"*\" roles=\"*\" verbs=\"*\" />"
                . "<add accessType=\"Deny\" users=\"*\" />"
                . "</authorization></security></system.webServer></configuration>\n"
            );
        }
    }

    /**
     * action globalErrorValidation
     *
     * @return string
     */
    public function globalErrorValidation(): string
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();

        $arrKeys = ['emails', 'emailSubject', 'compress', 'php', 'root', 'siteurl', 'cleanup', 'cleanupQuantity'];
        $arrValidation = [];
        foreach ($arrKeys as $key) {
            $arrValidation[$key] = LocalizationUtility::translate("global.error.$key", 'ns_backup');
        }

        $errorValidation = implode('', array_map(function ($key, $value) {
            return empty($this->globalSettingsData[0]->$key) ? '<li>' . $value . '</li>' : '';
        }, array_keys($arrValidation), $arrValidation));

        // Let's check configuration for PHPBU
        $arrGetLoadedExtensions = get_loaded_extensions();
        $arrExtensionsToCheck = ['curl', 'dom', 'json'];
        foreach ($arrExtensionsToCheck as $extension) {
            if (!in_array($extension, $arrGetLoadedExtensions)) {
                $errorValidation .= '<li>' . LocalizationUtility::translate("global.error.$extension", 'ns_backup') . '</li>';
            }
        }
        // Check if exec() works
        if (!exec('echo EXEC') == 'EXEC') {
            $errorValidation .= '<li>' . LocalizationUtility::translate('global.error.exec', 'ns_backup') . '</li>';
        }
        return $errorValidation;
    }

    /**
     * action generateBackup
     *
     * @param array $arrPost
     * @return array
     */
    public function generateBackup(array $arrPost): array
    {
        // Get global configuration
        $this->globalSettingsData = $this->backupglobalRepository->findAll();
        // Get PHP Path
        $this->phpPath = !empty($this->globalSettingsData[0]->php)
            ? $this->globalSettingsData[0]->php
            : (exec('which php') ?: 'php ');

        // Get TYPO3 Path
        $this->rootPath = $this->globalSettingsData[0]->root ?? (Environment::getProjectPath() ?? '');

        // Let's change root path to /public in Composer-based installation
        if (Environment::isComposerMode()) {
            $this->rootPath = Environment::getPublicPath();
            $typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
                VersionNumberUtility::getCurrentTypo3Version()
            );
            $typo3Version = $typo3VersionArray['version_main'];

            if ($typo3Version <= 13) {
                $this->composerRootPath = Environment::getComposerRootPath();
            } else {
                $this->composerRootPath = Environment::getProjectPath();
            }
            $this->phpbuPath = $this->composerRootPath.'/vendor/nitsan/ns-backup/phpbu.phar';
        }

        // Storage: never default into the public web root
        $storage = $this->resolveBackupStoragePaths($this->globalSettingsData[0] ?? null);
        $this->localStoragePath = $storage['localStoragePath'];
        $jsonFolder = $storage['jsonFolder'];

        try {
            if (!file_exists($this->localStoragePath)) {
                GeneralUtility::mkdir_deep($this->localStoragePath);
            }
            if (!file_exists($jsonFolder)) {
                GeneralUtility::mkdir_deep($jsonFolder);
            }
            $this->ensureBackupDirectoryProtection(rtrim($this->localStoragePath, '/'));
            $this->ensureBackupDirectoryProtection(rtrim($jsonFolder, '/'));
        } catch (RuntimeException $e) {
            return  [
                'log' => 'error',
                'backup_file' => $this->exceptionMessage,
            ];
        }

        // Get Base URL (kept for internal path building; public download URLs are no longer stored)
        $this->siteUrl = '';
        if (!empty($this->globalSettingsData[0]->siteurl)) {
            $this->siteUrl = $this->globalSettingsData[0]->siteurl;
        }
        $path = str_replace($this->rootPath, '', $storage['storePath']);
        $this->baseURL = $this->siteUrl . $path . '/tx_nsbackup/';

        // Get PHPHBU Path
        if (Environment::isComposerMode()) {
            $this->phpbuPath = $this->composerRootPath . '/vendor/nitsan/ns-backup/phpbu.phar';
        } else {
            $this->phpbuPath = $this->rootPath . '/typo3conf/ext/ns_backup/phpbu.phar';
        }

        // Get Database Configuration
        $this->arrDatabase = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];
        $this->arrDatabase['port'] = $this->arrDatabase['port'] ?? '3306';

        // Get Current Date time
        $this->prefixFileName = date('dmY_Hi');

        // Prepare backup filename
        $backupName = $arrPost['backupName'].'_'.$this->prefixFileName;
        $currentDateTime = '';
        $backupNameOriginal = $arrPost['backupName'];
        $backupFileName = strtolower(trim($backupName));
        $backupFileName = preg_replace(['/[\s-]+/', '/[^A-Za-z0-9_]/', '/_+/'], '_', $backupFileName);

        // Whitelist allowed backup types
        $allowedBackupTypes = ['mysqldump', 'typo3', 'vendor', 'typo3conf'];
        $backupType = $arrPost['backupFolderSettings'] ?? '';

        if (!in_array($backupType, $allowedBackupTypes, true)) {
            throw new RuntimeException('Invalid backup type specified.');
        }

        $randomString = bin2hex(random_bytes(8));
        $backupNameParts = GeneralUtility::trimExplode('_', (string)$backupFileName, true, 3);
        $backupBaseName = $backupNameParts[1] ?? 'backup';
        $backupBaseName = preg_replace('/[^A-Za-z0-9]/', '', $backupBaseName) ?: 'backup';

        $jsonFile = $backupBaseName . '_' . $randomString . '_' . $backupType . '_configuration.json';
        $logFile = $jsonFolder . $backupBaseName . '_' . $randomString . '_' . $backupType . '_log.json';
        $jsonPath = $jsonFolder . $jsonFile;

        // Prepare backup filename
        $backupFileName = preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            preg_replace('/[\s-]+/', '_', strtolower(trim($backupName)))
        );
        // Let's create LOG file if not existis
        if (!file_exists($logFile)) {
            $fh = @fopen($logFile, 'a');
            if ($fh != false) {
                @fclose($fh);
            }
        }

        // Email configuration
        $emailString = $this->globalSettingsData[0]->emails ?? '';
        $emailArray = array_map('trim', explode(',', $emailString));
        $validEmails = array_filter($emailArray, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        $emailRecipients = implode(',', $validEmails);
        $emailSubject = preg_replace('/[^A-Za-z0-9_\-\[\]\s]/', '', $this->globalSettingsData[0]->emailSubject ?? '');
        $emailNotificationOnError = $this->globalSettingsData[0]->emailNotificationOnError === '1' ? '1' : '0';

        // Prepare JSON configuration using json_encode for security
        $jsonConfig = [
            'verbose' => true,
            'debug' => false,
            'logging' => [
                [
                    'type' => 'json',
                    'target' => $logFile
                ],
                [
                    'type' => 'mail',
                    'options' => [
                        'transport' => 'mail',
                        'recipients' => $emailRecipients,
                        'subject' => "[{$backupType}] {$backupNameOriginal} - {$emailSubject}",
                        'sendOnlyOnError' => $emailNotificationOnError
                    ]
                ]
            ],
            'backups' => []
        ];

        // Let's check if admin wants "Backup Everything"
        if ($backupType == 'all') {
            // store date and time before backup
            $currentDateTime = date('Ymd-Hi');
            // Create Database Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, 'mysqldump', $backupFileName);
            // Create Code Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName);
        } elseif ($backupType == 'other') {
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName, $arrPost['custompath']);
        } else {
            // Create Specific Selected Type of Backup
            $jsonConfig['backups'][] = $this->getPhpbuBackupArray($backupName, $backupType, $backupFileName);
        }

        $json = json_encode($jsonConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $log = [];
        $return_var = 1;
        try {
            // Ensure the directory exists
            if (!file_exists($jsonFolder)) {
                GeneralUtility::mkdir($jsonFolder);
            }

            // Write JSON content to file with restrictive permissions
            file_put_contents($jsonPath, $json);
            @chmod($jsonPath, 0600);

            // Validate and sanitize PHP path
            if (!is_string($this->phpPath) || !file_exists($this->phpPath) || !is_executable($this->phpPath)) {
                throw new RuntimeException('Invalid PHP executable path.');
            }

            // Prepare secure shell command
            $phpBin = escapeshellcmd($this->phpPath);
            $phpbuBin = escapeshellarg($this->phpbuPath);
            $configArg = escapeshellarg('--configuration=' . $jsonPath);

            $command = "$phpBin $phpbuBin $configArg --verbose";

            // Execute Backup SSH Command
            exec($command, $log, $return_var);
        } catch (RuntimeException $e) {
            if (is_file($jsonPath)) {
                @unlink($jsonPath);
            }
            return  [
                'log' => 'error',
                'backup_file' => 'Something is wrong here.' . $e->getMessage(),
            ];
        } finally {
            // Never leave DB credentials on disk after the run
            if (isset($jsonPath) && is_file($jsonPath)) {
                @unlink($jsonPath);
            }
        }

        // Validate If SSH command success
        if (count($log) > 0 && is_array($log)) {
            $log = file_get_contents($logFile);

            // Keep log basename for cleanup; configuration JSON is already removed
            $arrPost['jsonfile'] = basename($logFile);

            // If Backup Everything, Then let's first-insert MySQL as special case
            if ($backupType == 'all') {
                $arrPost['backup_type'] = 'mysqldump';
                // Never store a public download URL (use backend download action)
                $arrPost['download_url'] = '';
                $compressTechnique = $this->globalSettingsData[0]->compress;
                $compressTechniques = [
                    'bzip2' => '.bz2',
                    'zip' => '',
                    'gzip' => '.gz',
                    'xz' => '.xz',
                ];

                $compressTechnique = $compressTechniques[$compressTechnique] ?? '.bz2';

                $path = str_replace('/all', "", $this->backupFilePath);
                $backupFileMySQL = $path . '/mysqldump' . '/mysqldump' . '-' . $currentDateTime . '.sql' . $compressTechnique;
                $fileSize = $this->convertFilesize(filesize($backupFileMySQL));
                $arrPost['size'] = $fileSize;

                $arrPost['filenames'] = $backupFileMySQL;
                $this->backupglobalRepository->addBackupData($arrPost);

            }

            // Insert to Database > Backup History
            $arrPost['download_url'] = '';
            $arrPost['log'] = $log;

            try {
                $fileSize = $this->convertFilesize(filesize($this->backupFile));
            } catch (\Exception $e) {
                return  [
                    'log' => 'error',
                    'backup_file' => 'Something is wrong here. Please check you Global Settings',
                ];
            }

            $arrPost['size'] = $fileSize;
            $arrPost['filenames'] = $this->backupFile;

            $insertId = $this->backupglobalRepository->addBackupData($arrPost);
            $arrReturn = [
                'log' => $log,
                'backup_file' => $this->backupFile,
                'download_url' => '',
                'uid' => $insertId,
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
     * Generate PHP BU backup configuration as array
     * @param string $backupName
     * @param string $backupType
     * @param string $backupFileName
     * @param string|null $rawName
     * @return array
     */
    protected function getPhpbuBackupArray(string $backupName, string $backupType, string $backupFileName, ?string $rawName = null): array
    {
        $ignoreUploads = '';
        $backupConfig = [
            'name' => $backupName
        ];

        $backupExtFile = '.tar';
        switch ($backupType) {
            case 'mysqldump':
                $backupConfig['source'] = [
                    'type' => 'mysqldump',
                    'options' => [
                        'host' => $this->arrDatabase['host'],
                        'port' => (string)$this->arrDatabase['port'],
                        'databases' => $this->arrDatabase['dbname'],
                        'user' => $this->arrDatabase['user'],
                        'password' => $this->arrDatabase['password']
                    ]
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

            if (Environment::isComposerMode() && $this->composerRootPath !== null && strlen($this->composerRootPath) > 0) {
                if ($backupType == 'vendor') {
                    $sourcePath = $this->composerRootPath . '/' . $targetPath;
                } elseif ($backupType == 'typo3') {
                    $sourcePath = $this->composerRootPath . '/vendor/typo3';
                }
            }

                $sourceOptions = [
                    'path' => ($backupType == 'other') ? $rawName : $sourcePath
                ];

                if (!empty($ignoreUploads)) {
                    $sourceOptions['exclude'] = $ignoreUploads;
                }

                $backupConfig['source'] = [
                    'type' => 'tar',
                    'options' => $sourceOptions
                ];
        }

        // PATCH If compress=bzip2
        $compressTechnique = $this->globalSettingsData[0]->compress;
        $compressTechniques = [
            'bzip2' => '.bz2',
            'zip' => '',
            'gzip' => '.gz',
            'xz' => '.xz',
        ];

        $compressTechnique = $compressTechniques[$compressTechnique] ?? '.bz2';

        $this->backupFilePath = $this->localStoragePath . $backupType;

        // Unpredictable filename token (replaces static md5(backupType))
        $this->backupFileToken = bin2hex(random_bytes(8));

        // Physical file
        $this->backupFile = $this->backupFilePath . '/' . $this->backupFileToken . '-' . date('Ymd-Hi') . $backupExtFile . $compressTechnique;

        // Legacy internal URL build (not persisted / not exposed)
        $this->backupDownloadPath =
            $this->baseURL .
            $backupType . '/' .
            $this->backupFileToken . '-' . date('Ymd-Hi') . $backupExtFile . $compressTechnique;

        // If Backup Type = ALL then, Let's consider mysql as special-case
        if ($backupType == 'mysqldump') {
            if ($this->globalSettingsData[0]->compress == 'zip') {
                $compressTechnique = '';
            }
            $this->backupFileMySQL = $this->backupFilePath . '/' . $this->backupFileToken . '-' . date('Ymd-Hi') . $backupExtFile . $compressTechnique;
            $this->backupDownloadPathMySQL =
                $this->baseURL .
                $backupType . '/' . $this->backupFileToken . '-' . date('Ymd-Hi') . $backupExtFile . $compressTechnique;
        }
        $this->backupFileName = $this->backupFileToken . '-%Y%m%d-%H%i' . $backupExtFile;

        $backupConfig['target'] = [
            'dirname' => $this->backupFilePath,
            'filename' => $this->backupFileName,
            'compress' => $this->globalSettingsData[0]->compress
        ];

        $backupConfig['cleanup'] = [
            'type' => $this->globalSettingsData[0]->cleanup,
            'options' => [
                'amount' => $this->globalSettingsData[0]->cleanupQuantity
            ]
        ];

        return $backupConfig;
    }

    /**
     * Convert File Size
     * @param mixed $bytes
     * @return string
     */
    protected function convertFilesize(mixed $bytes): string
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
        $path = rtrim($path, '/');
        if ($path === '') {
            return false;
        }

        $publicPath = rtrim(Environment::getPublicPath(), '/');
        if ($publicPath !== '' && (str_starts_with($path, $publicPath . '/') || $path === $publicPath)) {
            return true;
        }

        // Classic installations: treat common public docroot folders as public
        if (!Environment::isComposerMode()) {
            $publicMarkers = ['fileadmin', 'uploads', 'typo3temp'];
            $parts = array_filter(explode('/', $path));
            return count(array_intersect($parts, $publicMarkers)) > 0;
        }

        return false;
    }
}
