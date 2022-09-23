<?php
namespace NITSAN\NsBackup\Controller;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;

use NITSAN\NsBackup\Controller\BackupBaseController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as transalte;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility as debug;

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
 * BackupsController
 */
class BackupsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * backupglobalRepository
     */
    protected $backupglobalRepository;

    /**
     * Inject the BackupglobalRepository repository
     *
     * @param \NITSAN\NsBackup\Domain\Repository\BackupglobalRepository $backupglobalRepository
     */
    public function injectProductRepository(BackupglobalRepository $backupglobalRepository)
    {
        $this->backupglobalRepository = $backupglobalRepository;
    }

    /**
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     */
    public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        // Global error check
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->backupBaseController = $this->objectManager->get(BackupBaseController::class);

        $this->errorValidation = $this->backupBaseController->globalErrorValidation();
        if(!empty($this->errorValidation)) {
            $header = transalte::translate('global.errorvalidation','ns_backup');
            $message = transalte::translate('global.errorvalidation.message','ns_backup');
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
        $globalSettingsData = $this->backupglobalRepository->findAll();
        $arrBackupData = $this->backupglobalRepository->findBackupDataAll(5);
        if (version_compare(TYPO3_branch, '11', '>=')) {
            $this->view->assign('modalAttr','data-bs-');
        } else {
            $this->view->assign('modalAttr','data-');
        }

        $arrMultipleVars = [
            'cleanup' => constant('cleanup'),
            'backuptype' => constant('backuptype'),
            'compress' => constant('compress'),
            'backupglobal' => $globalSettingsData[0],
            'action' => 'dashboard',
            'arrBackupData' => $arrBackupData,
            'errorValidation' => $this->errorValidation
        ];

        $this->view->assignMultiple($arrMultipleVars);
    }

    /**
     * action backuprestore
     *
     * @return void
     */
    public function backuprestoreAction()
    {
        $globalSettingsData = $this->backupglobalRepository->findAll();
        if (version_compare(TYPO3_branch, '11', '>=')) {
            $this->view->assign('modalAttr','data-bs-');
        } else {
            $this->view->assign('modalAttr','data-');
        }
        $arrMultipleVars = [
            'cleanup' => constant('cleanup'),
            'backuptype' => constant('backuptype'),
            'compress' => constant('compress'),
            'backupglobal' => $globalSettingsData[0],
            'action' => 'backuprestore',
            'errorValidation' => $this->errorValidation
        ];

        ###########################
        ### Start Backup Module ###
        ###########################

        $arrPost = $this->request->getArguments();
        $schedulerId = isset($_REQUEST['schedulerId']) ? $_REQUEST['schedulerId'] : '';

        // "RUN" Backup from "Manual Backup Module"
        $arrPost['backuprestore'] = isset($arrPost['backuprestore']) ? $arrPost['backuprestore'] : '';
        $arrPost = $arrPost['backuprestore'];

        if(!empty($arrPost['backupFolderSettings'])) {

            // Create json and take backup
            //$arrResponse = BackupBaseController::generateBackup($arrPost);
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->backupBaseController = $this->objectManager->get(BackupBaseController::class);
            $arrResponse = $this->backupBaseController->generateBackup($arrPost);

            if($arrResponse['log'] == 'error') {
                // Error Flash-Message
                $mesHeader = transalte::translate('manualbackup.error','ns_backup');
                $backup_file = $arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            }
            else {
                // Success Flash-Message
                $mesHeader = transalte::translate('manualbackup.success','ns_backup');
                $backup_file = transalte::translate('backup.downloaded','ns_backup').' '.$arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader, \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);

                // Pass to Fluid
                $arrMultipleVars['isManualBackup'] = '1';
                $arrMultipleVars['log'] = '<pre class="pre-scrollable"><code class="json">'. json_encode(json_decode($arrResponse['log']), JSON_PRETTY_PRINT) .'</code></pre>';
                $arrMultipleVars['download_url'] = $arrResponse['download_url'];
            }

            //$this->redirect('backuprestore');
        }

        // List Backup History
        $objBackupData = $this->backupglobalRepository->findBackupDataAll();
        foreach ($objBackupData as $keyBackup => $valueBackup) {
            // Formate Log
            if ($objBackupData[$keyBackup]['logs']) {
                $objBackupData[$keyBackup]['logs'] = '<pre class="pre-scrollable"><code class="json">' . json_encode(json_decode($objBackupData[$keyBackup]['logs']), JSON_PRETTY_PRINT) . '</code></pre>';
            }
        }
        $arrMultipleVars['arrBackupData'] = $objBackupData;

        //session_start();
        //echo "<pre>";print_r($_SESSION);exit;

        $this->view->assignMultiple($arrMultipleVars);
    }

    /**
     * action premiumextension
     *
     * @return void
     */
    public function premiumextensionAction()
    {
        $arrMultipleVars = [
            'action' => 'premiumextension',
            'errorValidation' => $this->errorValidation
        ];

        $this->view->assignMultiple($arrMultipleVars);
    }

    /**
     * action deletebackupbackup
     *
     * @return void
     */
    public function deletebackupbackupAction()
    {
        $uid = GeneralUtility::_GP('uids');
        $arrBackup = $this->backupglobalRepository->findBackupByUid($uid);

        // Let's delete it
        $this->backupglobalRepository->removeBackupData($uid);

        // Remove file from Physical location
        if(file_exists($arrBackup['filenames'])){
            unlink($arrBackup['filenames']);
        }

        $headerMsg = transalte::translate('delete.backup.data','ns_backup');
        $msg = transalte::translate('delete.backup.message','ns_backup').$arrBackup['filenames'];
        $this->addFlashMessage($msg, $headerMsg, \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        return $msg;
        //$this->redirect('backuprestore');
    }
}
