<?php

namespace NITSAN\NsBackup\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
 * Test
 */
class Backupglobal extends AbstractEntity
{
    /**
     * emails
     *
     * @var string
     */
    public string $emails = '';

    /**
     * emailSubject
     *
     * @var string
     */
    public string $emailSubject = '';

    /**
     * emailNotificationOnError
     *
     * @var int|null
     */
    public ?int $emailNotificationOnError = 0;

    /**
     * emailNotificationOnSuccess
     *
     * @var int|null
     */
    public ?int $emailNotificationOnSuccess = 0;

    /**
     * defaultServer
     *
     * @var string
     */
    public string $defaultServer = '';

    /**
     * passwordRestore
     *
     * @var string
     */
    public string $passwordRestore = '';

    /**
     * backupValidation
     *
     * @var string
     */
    public string $backupValidation = '';

    /**
     * encryption
     *
     * @var string
     */
    public string $encryption = '';

    /**
     * compress
     *
     * @var string
     */
    public string $compress = '';

    /**
     * php
     *
     * @var string
     */
    public string $php = '';

    /**
     * root
     *
     * @var string
     */
    public string $root = '';

    /**
     * siteurl
     *
     * @var string
     */
    public string $siteurl = '';

    /**
     * cleanupLocalName
     *
     * @var string
     */
    public string $cleanupLocalName = '';

    /**
     * cleanupLocalValue
     *
     * @var string
     */
    public string $cleanupLocalValue = '';

    /**
     * cleanupServerName
     *
     * @var string
     */
    public string $cleanupServerName = '';

    /**
     * cleanupServerValue
     *
     * @var string
     */
    public string $cleanupServerValue = '';

    /**
     * quickSetupWizard
     *
     * @var int
     */
    public int $quickSetupWizard = 0;

    /**
     * cleanup
     *
     * @var string
     */
    public string $cleanup = '';

    /**
     * cleanupQuantity
     *
     * @var int
     */
    public int $cleanupQuantity = 0;

    /**
     * Returns the emailSubject
     *
     * @return string emailSubject
     */
    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    /**
     * Sets the emailSubject
     *
     * @param string $emailSubject
     * @return void
     */
    public function setEmailSubject(string $emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }

    /**
     * Returns the emails
     *
     * @return string emails
     */
    public function getEmails(): string
    {
        return $this->emails;
    }

    /**
     * Sets the emails
     *
     * @param string $emails
     * @return void
     */
    public function setEmails(string $emails): void
    {
        $this->emails = $emails;
    }

    /**
     * Returns the emailNotificationOnError
     *
     * @return int $emailNotificationOnError
     */
    public function getEmailNotificationOnError(): int
    {
        return $this->emailNotificationOnError;
    }

    /**
     * Sets the emailNotificationOnError
     *
     * @param int|null $emailNotificationOnError
     * @return void
     */
    public function setEmailNotificationOnError(?int $emailNotificationOnError)
    {
        $this->emailNotificationOnError = $emailNotificationOnError;
    }

    /**
     * Returns the emailNotificationOnSuccess
     *
     * @return int $emailNotificationOnSuccess
     */
    public function getEmailNotificationOnSuccess(): int
    {
        return $this->emailNotificationOnSuccess;
    }

    /**
     * Sets the emailNotificationOnSuccess
     *
     * @param int $emailNotificationOnSuccess
     * @return void
     */
    public function setEmailNotificationOnSuccess(int $emailNotificationOnSuccess): void
    {
        $this->emailNotificationOnSuccess = $emailNotificationOnSuccess;
    }

    /**
     * Returns the passwordRestore
     *
     * @return string $passwordRestore
     */
    public function getPasswordRestore(): string
    {
        return $this->passwordRestore;
    }

    /**
     * Sets the passwordRestore
     *
     * @param string $passwordRestore
     * @return void
     */
    public function setPasswordRestore(string $passwordRestore): void
    {
        $this->passwordRestore = $passwordRestore;
    }

    /**
     * Returns the backupValidation
     *
     * @return string $backupValidation
     */
    public function getBackupValidation(): string
    {
        return $this->backupValidation;
    }

    /**
     * Sets the backupValidation
     *
     * @param string $backupValidation
     * @return void
     */
    public function setBackupValidation(string $backupValidation): void
    {
        $this->backupValidation = $backupValidation;
    }

    /**
     * Returns the encryption
     *
     * @return string $encryption
     */
    public function getEncryption(): string
    {
        return $this->encryption;
    }

    /**
     * Sets the encryption
     *
     * @param string $encryption
     * @return void
     */
    public function setEncryption(string $encryption): void
    {
        $this->encryption = $encryption;
    }

    /**
     * Returns the compress
     *
     * @return string $compress
     */
    public function getCompress(): string
    {
        return $this->compress;
    }

    /**
     * Sets the compress
     *
     * @param string $compress
     * @return void
     */
    public function setCompress(string $compress): void
    {
        $this->compress = $compress;
    }

    /**
     * Returns the php
     *
     * @return string $php
     */
    public function getPhp(): string
    {
        return $this->php;
    }

    /**
     * Sets the php
     *
     * @param string $php
     * @return void
     */
    public function setPhp(string $php): void
    {
        $this->php = $php;
    }

    /**
     * Returns the root
     *
     * @return string $root
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Sets the root
     *
     * @param string $root
     * @return void
     */
    public function setRoot(string $root): void
    {
        $this->root = $root;
    }

    /**
     * Returns the siteurl
     *
     * @return string $siteurl
     */
    public function getSiteurl(): string
    {
        return $this->siteurl;
    }

    /**
     * Sets the siteurl
     *
     * @param string $siteurl
     * @return void
     */
    public function setSiteurl(string $siteurl): void
    {
        $this->siteurl = $siteurl;
    }

    /**
     * Returns the cleanupQuantity
     *
     * @return int $cleanupQuantity
     */
    public function getCleanupQuantity(): int
    {
        return $this->cleanupQuantity;
    }

    /**
     * Sets the cleanupQuantity
     *
     * @param int $cleanupQuantity
     * @return void
     */
    public function setcleanupQuantity(int $cleanupQuantity): void
    {
        $this->cleanupQuantity = $cleanupQuantity;
    }

    /**
     * Returns the cleanupLocalName
     *
     * @return string $cleanupLocalName
     */
    public function getCleanupLocalName(): string
    {
        return $this->cleanupLocalName;
    }

    /**
     * Sets the cleanupLocalName
     *
     * @param string $cleanupLocalName
     * @return void
     */
    public function setCleanupLocalName(string $cleanupLocalName): void
    {
        $this->cleanupLocalName = $cleanupLocalName;
    }

    /**
     * Returns the cleanupLocalValue
     *
     * @return string $cleanupLocalValue
     */
    public function getCleanupLocalValue(): string
    {
        return $this->cleanupLocalValue;
    }

    /**
     * Sets the cleanupLocalValue
     *
     * @param string $cleanupLocalValue
     * @return void
     */
    public function setCleanupLocalValue(string $cleanupLocalValue): void
    {
        $this->cleanupLocalValue = $cleanupLocalValue;
    }

    /**
     * Returns the cleanupServerName
     *
     * @return string $cleanupServerName
     */
    public function getCleanupServerName(): string
    {
        return $this->cleanupServerName;
    }

    /**
     * Sets the cleanupServerName
     *
     * @param string $cleanupServerName
     * @return void
     */
    public function setCleanupServerName(string $cleanupServerName): void
    {
        $this->cleanupServerName = $cleanupServerName;
    }

    /**
     * Returns the cleanupServerValue
     *
     * @return string $cleanupServerValue
     */
    public function getCleanupServerValue(): string
    {
        return $this->cleanupServerValue;
    }

    /**
     * Sets the cleanupServerValue
     *
     * @param string $cleanupServerValue
     * @return void
     */
    public function setCleanupServerValue(string $cleanupServerValue): void
    {
        $this->cleanupServerValue = $cleanupServerValue;
    }

    /**
     * Returns the quickSetupWizard
     *
     * @return int $quickSetupWizard
     */
    public function getQuickSetupWizard(): int
    {
        return $this->quickSetupWizard;
    }

    /**
     * Sets the quickSetupWizard
     *
     * @param int $quickSetupWizard
     * @return void
     */
    public function setQuickSetupWizard(int $quickSetupWizard): void
    {
        $this->quickSetupWizard = $quickSetupWizard;
    }

    /**
     * Returns the defaultServer
     *
     * @return string $defaultServer
     */
    public function getDefaultServer(): string
    {
        return $this->defaultServer;
    }

    /**
     * Sets the defaultServer
     *
     * @param string $defaultServer
     * @return void
     */
    public function setDefaultServer(string $defaultServer): void
    {
        $this->defaultServer = $defaultServer;
    }

    /**
     * Returns the cleanup
     *
     * @return string $cleanup
     */
    public function getCleanup(): string
    {
        return $this->cleanup;
    }

    /**
     * Sets the cleanup
     *
     * @param string $cleanup
     * @return void
     */
    public function setCleanup(string $cleanup): void
    {
        $this->cleanup = $cleanup;
    }
}
