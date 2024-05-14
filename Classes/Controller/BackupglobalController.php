<?php

namespace NITSAN\NsBackup\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use NITSAN\NsBackup\Domain\Model\Backupglobal;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
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
class BackupglobalController extends ActionController
{

    /**
     * errorValidation
     */
    protected $errorValidation;

    /**
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param BackupglobalRepository $backupglobalRepository
     * @param BackupBaseController $backupBaseController
     */
    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected BackupglobalRepository $backupglobalRepository,
        protected BackupBaseController $backupBaseController
    ) {
    }

    /**
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     */
    public function initializeView(): void
    {
        // Global error check
        $this->errorValidation = $this->backupBaseController->globalErrorValidation();
        if(!empty($this->errorValidation)) {
            $header = transalte::translate('global.errorvalidation', 'ns_backup');
            $message = transalte::translate('global.errorvalidation.message', 'ns_backup');
            $this->addFlashMessage($message, $header, ContextualFeedbackSeverity::ERROR);
        }
    }

    /**
     * action globalsetting
     * @return ResponseInterface
     */
    public function globalsettingAction(): ResponseInterface
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
     * @param Backupglobal $backupglobal
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     */
    public function createAction(Backupglobal $backupglobal): ResponseInterface
    {
        $emails = GeneralUtility::trimExplode(',',$backupglobal->getEmails());
        foreach ($emails as $email){
            if(!GeneralUtility::validEmail($email)){
                $msg = transalte::translate('email.not.valid','ns_backup');
                $this->addFlashMessage('', $msg);
                return $this->redirect('globalsetting',ContextualFeedbackSeverity::ERROR);
            }
        }

        $msg = transalte::translate('globalsettings.create', 'ns_backup');
        $this->addFlashMessage('', $msg);
        $this->backupglobalRepository->add($backupglobal);

        return $this->redirect('globalsetting');
    }

    /**
     * action update
     *
     * @param Backupglobal $backupglobal
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function updateAction(Backupglobal $backupglobal): ResponseInterface
    {
        $emails = GeneralUtility::trimExplode(',',$backupglobal->getEmails());
        foreach ($emails as $email){
            if(!GeneralUtility::validEmail($email)){
                $msg = transalte::translate('email.not.valid','ns_backup');
                $this->addFlashMessage('', $msg);
                return $this->redirect('globalsetting',ContextualFeedbackSeverity::ERROR);
            }
        }
        $msg = transalte::translate('globalsettings.update', 'ns_backup');
        $this->addFlashMessage('', $msg);
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
