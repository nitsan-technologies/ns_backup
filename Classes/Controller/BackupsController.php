<?php
namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
 * BackupsController
 */
class BackupsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * backupglobalRepository
     */
    protected $backupglobalRepository;
    protected $backupBaseController = null;
    protected $errorValidation      = null;
    protected $objectManager        = null;

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
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @extensionScannerIgnoreLine
     */
    public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        // Global error check
        //@extensionScannerIgnoreLine
        $this->objectManager        = GeneralUtility::makeInstance(ObjectManager::class);
        $this->backupBaseController = $this->objectManager->get(\NITSAN\NsBackup\Controller\BackupBaseController::class);

        $this->errorValidation = $this->backupBaseController->globalErrorValidation();
        if (! empty($this->errorValidation)) {
            $header  = transalte::translate('global.errorvalidation', 'ns_backup');
            $message = transalte::translate('global.errorvalidation.message', 'ns_backup');
            $this->addFlashMessage($message, $header, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
    }

    /**
     * action dashboard
     *
     * @return void
     */
    public function dashboardAction()
    {
        // Load JavaScript modules
        $pageRenderer = $this->objectManager->get(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/NsBackup/jquery');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/NsBackup/Main');

        $globalSettingsData = $this->backupglobalRepository->findAll();
        $arrBackupData      = $this->backupglobalRepository->findBackupDataAll(5);
        $arrMultipleVars    = [
            'cleanup'         => constant('cleanup'),
            'backuptype'      => constant('backuptype'),
            'compress'        => constant('compress'),
            'backupglobal'    => ! empty($globalSettingsData[0]) ? $globalSettingsData[0] : null,
            'action'          => 'dashboard',
            'arrBackupData'   => $arrBackupData,
            'errorValidation' => $this->errorValidation,
        ];
        // @extensionScannerIgnoreLine
        if (version_compare(TYPO3_branch, '11', '>=')) {
            $arrMultipleVars['modalAttr'] = 'data-bs-';
        } else {
            $arrMultipleVars['modalAttr'] = 'data-';
        }
        $this->view->assignMultiple($arrMultipleVars);
    }

    /**
     * action backuprestore
     *
     * @return void
     */
    public function backuprestoreAction()
    {
        $pageRenderer = $this->objectManager->get(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/NsBackup/jquery');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/NsBackup/Main');

        $globalSettingsData = $this->backupglobalRepository->findAll();
        if (! empty($globalSettingsData[0])) {
            $globalBackupStorePath = $globalSettingsData[0]->getBackupStorePath();
            $isPublicPath          = $this->isPathPublic($globalBackupStorePath);
        }

        $arrMultipleVars = [
            'cleanup'         => constant('cleanup'),
            'backuptype'      => constant('backuptype'),
            'compress'        => constant('compress'),
            'backupglobal'    => ! empty($globalSettingsData[0]) ? $globalSettingsData[0] : null,
            'action'          => 'backuprestore',
            'errorValidation' => $this->errorValidation,
        ];

        $arrPost    = $this->request->getArguments();
        $backupName = trim($arrPost['backuprestore']['backupName'] ?? '');
        if (! empty($backupName) && preg_match('/[^0-9A-Za-z _-]/', $backupName)) {
            $sanitizedName = htmlspecialchars($backupName, ENT_QUOTES, 'UTF-8');

            $this->addFlashMessage(
                "Invalid backup name: '{$sanitizedName}'. " . transalte::translate('manualbackup.error.description', 'ns_backup'),
                transalte::translate('manualbackup.error', 'ns_backup'),
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );

            $this->redirect('backuprestore');
            return;
        }

        // "RUN" Backup from "Manual Backup Module"
        $arrPost = $arrPost['backuprestore'] ?? '';

        if (! empty($arrPost['backupFolderSettings']) && empty($this->errorValidation)) {

            // Create json and take backup
            try {
                $arrResponse = $this->backupBaseController->generateBackup($arrPost);
            } catch (RuntimeException $e) {
                $this->addFlashMessage($e->getMessage(), transalte::translate('manualbackup.error', 'ns_backup'), \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                $this->redirect('backuprestore');
                return;
            }
            if ($arrResponse['log'] == 'error') {
                // Error Flash-Message
                $mesHeader   = transalte::translate('manualbackup.error', 'ns_backup');
                $backup_file = $arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            } else {
                $mesHeader   = transalte::translate('manualbackup.success', 'ns_backup');
                $backup_file = transalte::translate('backup.downloaded', 'ns_backup') . ' ' . $arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader);
                $response = (array) json_decode($arrResponse['log']);
                if (isset($response['errorCount']) && $response['errorCount'] > 0) {
                    $globalSettingsData = $this->backupglobalRepository->findAll();
                    if (! empty($globalSettingsData[0]) && $globalSettingsData[0]->getEmailNotificationOnError()) {
                        $emails = GeneralUtility::trimExplode(',', $globalSettingsData[0]->getEmails(), true);
                        foreach ($emails as $email) {
                            $mail = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
                            $mail->setFrom([$globalSettingsData[0]->getEmailFrom() => 'Backup']);
                            $mail->setTo([$email]);
                            $mail->setSubject($globalSettingsData[0]->getEmailSubject());
                            $mail->setBody('<p><strong>Backup Error:</strong> \'' . $response['errors'][0]->message . '\'</p>', 'text/html');
                            $mail->send();
                        }
                        $this->addFlashMessage(
                            $response['errors'][0]->message,
                            transalte::translate('manualbackup.warning', 'ns_backup'),
                            \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                        );
                    }
                }
                $arrMultipleVars['isManualBackup'] = '1';
                $arrMultipleVars['log']            = '<pre class="pre-scrollable"><code class="json">' . json_encode(json_decode($arrResponse['log']), JSON_PRETTY_PRINT) . '</code></pre>';
                $arrMultipleVars['download_url']   = '';
                if ($isPublicPath) {
                    $arrMultipleVars['download_url'] = $arrResponse['download_url'];
                }
            }
        }
        // List Backup History
        $objBackupData = $this->backupglobalRepository->findBackupDataAll();
        foreach ($objBackupData as $keyBackup => $valueBackup) {
            // Formate Log
            if ($objBackupData[$keyBackup]['logs']) {
                $objBackupData[$keyBackup]['logs'] = '<pre class="pre-scrollable"><code class="json">' . json_encode(json_decode($objBackupData[$keyBackup]['logs']), JSON_PRETTY_PRINT) . '</code></pre>';
            }
            if ($valueBackup['download_url']) {
                $file_headers                            = @get_headers($valueBackup['download_url']);
                $objBackupData[$keyBackup]['isDownload'] = (! $file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') ? false : true;
            }
        }
        //@extensionScannerIgnoreLine
        if (version_compare(TYPO3_branch, '11', '>=')) {
            $arrMultipleVars['modalAttr'] = 'data-bs-';
        } else {
            $arrMultipleVars['modalAttr'] = 'data-';
        }
        $arrMultipleVars['arrBackupData'] = $objBackupData;
        $this->view->assignMultiple($arrMultipleVars);
    }

    /**
     * deletebackupbackupAction
     * @return string
     */
    public function deletebackupbackupAction()
    {
        $uid = GeneralUtility::_GP('uid');

        $globalSettingsData = $this->backupglobalRepository->findAll();
        $globalSettingsData = ! empty($globalSettingsData[0]) ? $globalSettingsData[0] : null;

        if (! $globalSettingsData) {
            $headerMsg = transalte::translate('something.wrong.here', 'ns_backup');
            $this->addFlashMessage($headerMsg, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR, true);
            return $headerMsg;
        }

        $arrBackup = $this->backupglobalRepository->findBackupByUid($uid);
        // Let's delete it
        $this->backupglobalRepository->removeBackupData($uid);

        // Remove file from Physical location
        if (file_exists($arrBackup['filenames'])) {
            unlink($arrBackup['filenames']);
        }

        if (Environment::isComposerMode()) {
            $rootPath = Environment::getPublicPath() ?: PATH_site;
        }
        $rootPath   = $globalSettingsData->getBackupStorePath() ?? ($rootPath . '/uploads');
        $jsonFolder = $rootPath . '/tx_nsbackup/json/';
        if (file_exists($jsonFolder . $arrBackup['jsonfile'])) {
            unlink($jsonFolder . $arrBackup['jsonfile']);
        }

        $jsonLogFile = str_replace("_configuration", "_log", $arrBackup['jsonfile']);
        if (file_exists($jsonFolder . $jsonLogFile)) {
            unlink($jsonFolder . $jsonLogFile);
        }

        $headerMsg = transalte::translate('delete.backup.data', 'ns_backup');
        $msg       = transalte::translate('delete.backup.message', 'ns_backup') . $arrBackup['filenames'];
        $this->addFlashMessage($msg, $headerMsg, \TYPO3\CMS\Core\Messaging\AbstractMessage::OK, true);
        return $msg;
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
