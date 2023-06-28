<?php

namespace NITSAN\NsBackup\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use NITSAN\NsBackup\Controller\BackupBaseController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
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

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        BackupglobalRepository $backupglobalRepository
    ) {
        $this->backupglobalRepository = $backupglobalRepository;
    }

    /**
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     */
    public function initializeView()
    {
        // Global error check
        $this->backupBaseController = GeneralUtility::makeInstance(BackupBaseController::class);
        $this->errorValidation = $this->backupBaseController->globalErrorValidation();
        if(!empty($this->errorValidation)) {
            $header = transalte::translate('global.errorvalidation', 'ns_backup');
            $message = transalte::translate('global.errorvalidation.message', 'ns_backup');
            $this->addFlashMessage($message, $header, \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
        }
    }

    /**
     * action globalsetting
     *
     */
    public function globalsettingAction()
    {
        $view = $this->initializeModuleTemplate($this->request);
        $globalSettingsData = $this->backupglobalRepository->findAll();
        $view->assignMultiple([
            'cleanup' => constant('cleanup'),
            'backupglobal' => $globalSettingsData[0],
            'compress' => constant('compress'),
            'action' => 'globalsetting',
            'errorValidation' => $this->errorValidation,
            'modalAttr' => 'data-bs-'
        ]);
        return $view->renderResponse();
    }

    /**
     * action create
     *
     * @param \NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal
     */
    public function createAction(\NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal)
    {
        $msg = transalte::translate('globalsettings.create', 'ns_backup');
        $this->addFlashMessage('', $msg, \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK);
        $this->backupglobalRepository->add($backupglobal);

        return $this->redirect('globalsetting');
    }

    /**
     * action update
     *
     * @param \NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal
     */
    public function updateAction(\NITSAN\NsBackup\Domain\Model\Backupglobal $backupglobal)
    {
        $msg = transalte::translate('globalsettings.update', 'ns_backup');
        $this->addFlashMessage('', $msg, \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK);
        $this->backupglobalRepository->update($backupglobal);

        return $this->redirect('globalsetting');
    }

    /**
     * Initialize Module Template
     */
    protected function initializeModuleTemplate(
        ServerRequestInterface $request
    ): ModuleTemplate {
        return $this->moduleTemplateFactory->create($request);
    }
}
