<?php
namespace NITSAN\NsBackup\Controller;

use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
 * BackupglobalController
 */
class BackupglobalController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * backupglobalRepository
     */
    protected $backupglobalRepository;

    /**
     * backupBaseController
     */
    protected $backupBaseController;

    /**
     * errorValidation
     */
    protected $errorValidation;

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
        $this->objectManager        = GeneralUtility::makeInstance(ObjectManager::class);
        $this->backupBaseController = $this->objectManager->get(BackupBaseController::class);
        $this->errorValidation      = $this->backupBaseController->globalErrorValidation();
        if (! empty($this->errorValidation)) {
            $header  = LocalizationUtility::translate('global.errorvalidation', 'ns_backup');
            $message = LocalizationUtility::translate('global.errorvalidation.message', 'ns_backup');
            $this->addFlashMessage($message, $header, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
    }

    /**
     * action globalsetting
     *
     * @return void
     */
    public function globalsettingAction()
    {
        $globalSettingsData = $this->backupglobalRepository->findAll();
        $varPath            = Environment::getVarPath();
        $this->view->assignMultiple([
            'cleanup'         => constant('cleanup'),
            'backupglobal'    => $globalSettingsData[0],
            'compress'        => constant('compress'),
            'action'          => 'globalsetting',
            'errorValidation' => $this->errorValidation,
            'modalAttr'       => 'data-bs-',
            'varPath'         => $varPath,
        ]);
    }

    /**
     * action create
     *
     * @param \NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal
     * @return void
     */
    public function createAction(\NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal)
    {

        if (! is_dir($backupglobal->getBackupStorePath())) {

            $msg = LocalizationUtility::translate('storePath.not.valid', 'ns_backup');
            $this->addFlashMessage('', $msg, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            return $this->redirect('globalsetting');
        }
        $phpPath = trim($backupglobal->getPhp());
        $backupglobal->setPhp($phpPath);
        if (! is_executable($backupglobal->getPhp())) {
            $msg = LocalizationUtility::translate('phpPath.not.valid', 'ns_backup');
            $this->addFlashMessage('', $msg, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            return $this->redirect('globalsetting');
        }
        $msg = LocalizationUtility::translate('globalsettings.create', 'ns_backup');

        $this->addFlashMessage(
            $msg,
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );
        $this->backupglobalRepository->add($backupglobal);
        $this->redirect('globalsetting');
    }

    /**
     * action update
     *
     * @param \NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal
     * @return void
     */
    public function updateAction(\NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal)
    {
        if (! is_dir($backupglobal->getBackupStorePath())) {
            $msg = LocalizationUtility::translate('storePath.not.valid', 'ns_backup');
            $this->addFlashMessage(
                $msg,
                '',
                FlashMessage::ERROR
            );
            return $this->redirect('globalsetting');
        }

        $phpPath = trim($backupglobal->getPhp());
        $backupglobal->setPhp($phpPath);

        if (! is_executable($backupglobal->getPhp())) {
            $msg = LocalizationUtility::translate('phpPath.not.valid', 'ns_backup');
            $this->addFlashMessage(
                $msg,
                '',
                FlashMessage::ERROR
            );
            return $this->redirect('globalsetting');
        }

        $msg = LocalizationUtility::translate('globalsettings.update', 'ns_backup');
        $this->addFlashMessage(
            $msg,
            '',
            FlashMessage::OK
        );
        $this->backupglobalRepository->update($backupglobal);
        $this->redirect('globalsetting');
    }
}
