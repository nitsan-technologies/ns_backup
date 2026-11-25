<?php

namespace NITSAN\NsBackup\Controller;

use Doctrine\DBAL\Exception;
use NITSAN\NsBackup\Domain\Repository\BackupglobalRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
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
 * BackupsController
 */
class BackupsController extends ActionController
{
    protected const NS_LICENSE_MODULE = 'nitsan_nslicensemodule';
    protected const NS_LICENSE_FREE_SECTION_ANCHOR = '#free-section';

    /**
     * errorValidation
     */
    protected string $errorValidation;

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
     *
     */
    public function initializeView(): void
    {
        // Global error check
        $this->errorValidation = $this->backupBaseController->globalErrorValidation();
        if (!empty($this->errorValidation)) {
            $header = transalte::translate('global.errorvalidation', 'ns_backup');
            $message = transalte::translate('global.errorvalidation.message', 'ns_backup');
            $this->addFlashMessage($message, $header, ContextualFeedbackSeverity::ERROR);
        }
    }

    /**
     * action dashboard
     * @return ResponseInterface
     * @throws Exception
     */
    public function dashboardAction(): ResponseInterface
    {
        $pageRenderer = GeneralUtility::makeInstance(className: PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@nitsan/ns-backup/jquery.js');
        $pageRenderer->loadJavaScriptModule('@nitsan/ns-backup/Main.js');

        $globalSettingsData = $this->backupglobalRepository->findAll();
        $arrBackupData = $this->backupglobalRepository->findBackupDataAll(5);
        $trialUrl = (string)GeneralUtility::makeInstance(UriBuilder::class)
            ->buildUriFromRoute(self::NS_LICENSE_MODULE, ['trial' => 'ns_backup'])
            . self::NS_LICENSE_FREE_SECTION_ANCHOR;

        $arrMultipleVars = [
            'cleanup' => constant('cleanup'),
            'backuptype' => constant('backuptype'),
            'compress' => constant('compress'),
            'backupglobal' => $globalSettingsData[0],
            'action' => 'dashboard',
            'arrBackupData' => $arrBackupData,
            'errorValidation' => $this->errorValidation,
            'modalAttr' => 'data-bs-',
            'trialUrl' => $trialUrl,
        ];
        $view = $this->initializeModuleTemplate($this->request);
        $view->assignMultiple($arrMultipleVars);
        return $view->renderResponse('Backups/Dashboard');
    }

    /**
     * action backuprestore
     * @return ResponseInterface
     * @throws Exception
     */
    public function backuprestoreAction(): ResponseInterface
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@nitsan/ns-backup/jquery.js');
        $pageRenderer->loadJavaScriptModule('@nitsan/ns-backup/Main.js');

        $globalSettingsData = $this->backupglobalRepository->findAll();
        $arrMultipleVars = [
            'cleanup' => constant('cleanup'),
            'backuptype' => constant('backuptype'),
            'compress' => constant('compress'),
            'backupglobal' => $globalSettingsData[0] ?? null,
            'action' => 'backuprestore',
            'errorValidation' => $this->errorValidation
        ];

        $arrPost = $this->request->getArguments();
        $backupName = trim($arrPost['backuprestore']['backupName'] ?? '');
        if (preg_match('/[^0-9A-Za-z _-]/', $backupName)) {
            $sanitizedName = htmlspecialchars($backupName, ENT_QUOTES, 'UTF-8');

            $this->addFlashMessage(
                "Invalid backup name: '{$sanitizedName}'. " . transalte::translate('manualbackup.error.description', 'ns_backup'),
                transalte::translate('manualbackup.error', 'ns_backup'),
                ContextualFeedbackSeverity::ERROR
            );

            return $this->redirect('backuprestore');
        }

        // "RUN" Backup from "Manual Backup Module"
        $arrPost = $arrPost['backuprestore'] ?? '';

        if (!empty($arrPost['backupFolderSettings']) && empty($this->errorValidation)) {

            // Create json and take backup
            try {
                $arrResponse = $this->backupBaseController->generateBackup($arrPost);
            } catch (RuntimeException $e) {
                $this->addFlashMessage($e->getMessage(), transalte::translate('manualbackup.error', 'ns_backup'), ContextualFeedbackSeverity::ERROR);
                return $this->redirect('backuprestore');
            }

            if ($arrResponse['log'] == 'error') {
                // Error Flash-Message
                $mesHeader = transalte::translate('manualbackup.error', 'ns_backup');
                $backup_file = $arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader, ContextualFeedbackSeverity::ERROR);
            } else {
                // Success Flash-Message
                $mesHeader = transalte::translate('manualbackup.success', 'ns_backup');
                $backup_file = transalte::translate('backup.downloaded', 'ns_backup').' '.$arrResponse['backup_file'];
                $this->addFlashMessage($backup_file, $mesHeader);

                $response = (array) json_decode($arrResponse['log']);
                if (isset($response['errorCount']) && $response['errorCount'] > 0) {
                    $globalSettingsData = $this->backupglobalRepository->findAll();
                    if ($globalSettingsData[0]->emailNotificationOnError) {
                        $mail = GeneralUtility::makeInstance(MailMessage::class);
                        $emails = explode(',', $globalSettingsData[0]->emails);
                        foreach ($emails as $email) {
                            $mail->from(new Address($globalSettingsData[0]->emailFrom, 'Backup'));
                            $mail->to(
                                new Address($email)
                            );
                            $mail->subject($globalSettingsData[0]->emailSubject);
                            $errorMessage = htmlspecialchars((string)($response['errors'][0]->message ?? ''), ENT_QUOTES, 'UTF-8');
                            $mail->html('<p><strong>Backup Error:</strong> \''.$errorMessage.'\'</p>');
                            $mail->send();
                        }
                        $this->addFlashMessage($response['errors'][0]->message, transalte::translate('manualbackup.warning', 'ns_backup'), ContextualFeedbackSeverity::WARNING);
                    }
                }

                // Pass to Fluid — authenticated backend download (no public URL)
                $arrMultipleVars['isManualBackup'] = '1';
                $arrMultipleVars['log'] = '<pre class="pre-scrollable"><code class="json">'. json_encode(json_decode($arrResponse['log']), JSON_PRETTY_PRINT) .'</code></pre>';
                $arrMultipleVars['downloadUid'] = (int)($arrResponse['uid'] ?? 0);
            }
        }

        // List Backup History
        $storage = $this->backupBaseController->resolveBackupStoragePaths($globalSettingsData[0] ?? null);
        $objBackupData = $this->backupglobalRepository->findBackupDataAll();
        foreach ($objBackupData as $keyBackup => $valueBackup) {
            // Formate Log
            if ($valueBackup['logs']) {
                $objBackupData[$keyBackup]['logs'] = '<pre class="pre-scrollable"><code class="json">' . json_encode(json_decode($objBackupData[$keyBackup]['logs']), JSON_PRETTY_PRINT) . '</code></pre>';
            }
            // Local file presence only — no remote get_headers (SSRF)
            $filename = (string)($valueBackup['filenames'] ?? '');
            $objBackupData[$keyBackup]['isDownload'] = $filename !== '' && is_file($filename);
        }
        $arrMultipleVars['arrBackupData'] = $objBackupData;
        $arrMultipleVars['modalAttr'] = 'data-bs-';
        $arrMultipleVars['backupStorePath'] = $storage['storePath'];
        $view = $this->initializeModuleTemplate($this->request);
        $view->assignMultiple($arrMultipleVars);
        return $view->renderResponse('Backups/Backuprestore');
    }

    /**
     * Authenticated download of a backup file (admin module only).
     */
    public function downloadbackupAction(): ResponseInterface
    {
        $uid = (int)($this->request->hasArgument('uid') ? $this->request->getArgument('uid') : 0);
        if ($uid <= 0) {
            $this->addFlashMessage(
                transalte::translate('something.wrong.here', 'ns_backup'),
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('backuprestore');
        }

        $arrBackup = $this->backupglobalRepository->findBackupByUid($uid);
        if ($arrBackup === false || empty($arrBackup['filenames'])) {
            $this->addFlashMessage(
                transalte::translate('servercloud.content.downloadBackupNotFound', 'ns_backup')
                    ?: 'Backup file not found.',
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('backuprestore');
        }

        $filePath = (string)$arrBackup['filenames'];
        $globalSettingsData = $this->backupglobalRepository->findAll();
        $storage = $this->backupBaseController->resolveBackupStoragePaths($globalSettingsData[0] ?? null);

        // Allow legacy files still under a previously configured public store, but never path-traverse
        $allowedRoots = array_filter([
            $storage['storePath'],
            $storage['localStoragePath'],
            Environment::getVarPath(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
        ]);

        $isAllowed = false;
        foreach ($allowedRoots as $root) {
            if ($this->backupBaseController->isPathInsideDirectory($filePath, (string)$root)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed || !is_file($filePath) || !is_readable($filePath)) {
            $this->addFlashMessage(
                transalte::translate('servercloud.content.downloadBackupNotFound', 'ns_backup')
                    ?: 'Backup file not found.',
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('backuprestore');
        }

        $basename = basename($filePath);
        $stream = $this->streamFactory->createStreamFromFile($filePath);

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader(
                'Content-Disposition',
                'attachment; filename="' . rawurlencode($basename) . '"; filename*=UTF-8\'\'' . rawurlencode($basename)
            )
            ->withHeader('Content-Length', (string)filesize($filePath))
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withBody($stream);
    }

    /**
     * action deletebackupbackup
     * @return ResponseInterface
     * @throws Exception
     */
    public function deletebackupbackupAction(): ResponseInterface
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->buildJsonResponse([
                'success' => false,
                'message' => 'Method not allowed',
            ], 405);
        }

        $parsedBody = $this->request->getParsedBody();
        $parsedBody = is_array($parsedBody) ? $parsedBody : [];
        $uid = (int)(
            $parsedBody['uid']
            ?? ($this->request->hasArgument('uid') ? $this->request->getArgument('uid') : 0)
        );

        $globalSettingsData = $this->backupglobalRepository->findAll();
        $globalSettingsData = !empty($globalSettingsData[0]) ? $globalSettingsData[0] : null;

        if (!$globalSettingsData || $uid <= 0) {
            return $this->buildJsonResponse([
                'success' => false,
                'message' => transalte::translate('something.wrong.here', 'ns_backup'),
            ], 400);
        }

        $arrBackup = $this->backupglobalRepository->findBackupByUid($uid);
        if ($arrBackup === false) {
            return $this->buildJsonResponse([
                'success' => false,
                'message' => transalte::translate('something.wrong.here', 'ns_backup'),
            ], 404);
        }

        $storage = $this->backupBaseController->resolveBackupStoragePaths($globalSettingsData);
        $allowedRoots = array_filter([
            $storage['storePath'],
            $storage['localStoragePath'],
            Environment::getVarPath(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
        ]);

        // Remove file from physical location when it stays inside an allowed root
        $filename = (string)($arrBackup['filenames'] ?? '');
        if ($filename !== '' && is_file($filename)) {
            foreach ($allowedRoots as $root) {
                if ($this->backupBaseController->isPathInsideDirectory($filename, (string)$root)) {
                    @unlink($filename);
                    break;
                }
            }
        }

        $jsonFolder = $storage['jsonFolder'];
        $jsonName = basename((string)($arrBackup['jsonfile'] ?? ''));
        if ($jsonName !== '') {
            $candidates = [$jsonName];
            if (str_contains($jsonName, '_configuration')) {
                $candidates[] = str_replace('_configuration', '_log', $jsonName);
            }
            if (str_contains($jsonName, '_log')) {
                $candidates[] = str_replace('_log', '_configuration', $jsonName);
            }
            foreach (array_unique($candidates) as $candidate) {
                $candidatePath = $jsonFolder . $candidate;
                if (is_file($candidatePath)
                    && $this->backupBaseController->isPathInsideDirectory($candidatePath, $storage['storePath'])
                ) {
                    @unlink($candidatePath);
                }
            }
        }

        $this->backupglobalRepository->removeBackupData($uid);

        return $this->buildJsonResponse([
            'success' => true,
            'message' => transalte::translate('delete.backup.message', 'ns_backup') . $filename,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function buildJsonResponse(array $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->responseFactory->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($this->streamFactory->createStream((string)json_encode($payload)));
    }

    /**
     * Initialize Module Template
     */
    protected function initializeModuleTemplate(
        ServerRequestInterface $request
    ): ModuleTemplate {
        return $this->moduleTemplateFactory->create($request);
    }

    /**
     * @param string $path
     * @return boolean
     */
    public function isPathPublic(string $path): bool
    {
        return $this->backupBaseController->isPathPublic($path);
    }
}
