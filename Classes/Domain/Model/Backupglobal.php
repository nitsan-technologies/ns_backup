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
    public $emails = '';

    /**
     * emailSubject
     *
     * @var string
     */
    public $emailSubject = '';

    /**
     * emailNotificationOnError
     *
     * @var int
     */
    public $emailNotificationOnError = 0;

    /**
     * emailNotificationOnSuccess
     *
     * @var int
     */
    public $emailNotificationOnSuccess = 0;

    /**
     * defaultServer
     *
     * @var string
     */
    public $defaultServer = '';

    /**
     * passwordRestore
     *
     * @var string
     */
    public $passwordRestore = '';

    /**
     * backupValidation
     *
     * @var string
     */
    public $backupValidation = '';

    /**
     * encryption
     *
     * @var string
     */
    public $encryption = '';

    /**
     * compress
     *
     * @var string
     */
    public $compress = '';

    /**
     * php
     *
     * @var string
     */
    public $php = '';

    /**
     * root
     *
     * @var string
     */
    public $root = '';

    /**
     * siteurl
     *
     * @var string
     */
    public $siteurl = '';

    /**
     * cleanupLocalName
     *
     * @var string
     */
    public $cleanupLocalName = '';

    /**
     * cleanupLocalValue
     *
     * @var string
     */
    public $cleanupLocalValue = '';

    /**
     * cleanupServerName
     *
     * @var string
     */
    public $cleanupServerName = '';

    /**
     * cleanupServerValue
     *
     * @var string
     */
    public $cleanupServerValue = '';

    /**
     * quickSetupWizard
     *
     * @var int
     */
    public $quickSetupWizard = 0;

    /**
     * cleanup
     *
     * @var string
     */
    public $cleanup = '';

    /**
     * cleanupQuantity
     *
     * @var int
     */
    public $cleanupQuantity = 0;

    /**
     * Returns the emailSubject
     *
     * @return string emailSubject
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * Sets the emailSubject
     *
     * @param string $emailSubject
     * @return void
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;
    }

    /**
     * Returns the emails
     *
     * @return string emails
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Sets the emails
     *
     * @param string $emails
     * @return void
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * Returns the emailNotificationOnError
     *
     * @return int $emailNotificationOnError
     */
    public function getEmailNotificationOnError()
    {
        return $this->emailNotificationOnError;
    }

    /**
     * Sets the emailNotificationOnError
     *
     * @param int $emailNotificationOnError
     * @return void
     */
    public function setEmailNotificationOnError($emailNotificationOnError)
    {
        $this->emailNotificationOnError = $emailNotificationOnError;
    }

    /**
     * Returns the emailNotificationOnSuccess
     *
     * @return int $emailNotificationOnSuccess
     */
    public function getEmailNotificationOnSuccess()
    {
        return $this->emailNotificationOnSuccess;
    }

    /**
     * Sets the emailNotificationOnSuccess
     *
     * @param int $emailNotificationOnSuccess
     * @return void
     */
    public function setEmailNotificationOnSuccess($emailNotificationOnSuccess)
    {
        $this->emailNotificationOnSuccess = $emailNotificationOnSuccess;
    }

    /**
     * Returns the passwordRestore
     *
     * @return string $passwordRestore
     */
    public function getPasswordRestore()
    {
        return $this->passwordRestore;
    }

    /**
     * Sets the passwordRestore
     *
     * @param string $passwordRestore
     * @return void
     */
    public function setPasswordRestore($passwordRestore)
    {
        $this->passwordRestore = $passwordRestore;
    }

    /**
     * Returns the backupValidation
     *
     * @return string $backupValidation
     */
    public function getBackupValidation()
    {
        return $this->backupValidation;
    }

    /**
     * Sets the backupValidation
     *
     * @param string $backupValidation
     * @return void
     */
    public function setBackupValidation($backupValidation)
    {
        $this->backupValidation = $backupValidation;
    }

    /**
     * Returns the encryption
     *
     * @return string $encryption
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * Sets the encryption
     *
     * @param string $encryption
     * @return void
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * Returns the compress
     *
     * @return string $compress
     */
    public function getCompress()
    {
        return $this->compress;
    }

    /**
     * Sets the compress
     *
     * @param string $compress
     * @return void
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * Returns the php
     *
     * @return string $php
     */
    public function getPhp()
    {
        return $this->php;
    }

    /**
     * Sets the php
     *
     * @param string $php
     * @return void
     */
    public function setPhp($php)
    {
        $this->php = $php;
    }

    /**
     * Returns the root
     *
     * @return string $root
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets the root
     *
     * @param string $root
     * @return void
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Returns the siteurl
     *
     * @return string $siteurl
     */
    public function getSiteurl()
    {
        return $this->siteurl;
    }

    /**
     * Sets the siteurl
     *
     * @param string $siteurl
     * @return void
     */
    public function setSiteurl($siteurl)
    {
        $this->siteurl = $siteurl;
    }

    /**
     * Returns the cleanupQuantity
     *
     * @return int $cleanupQuantity
     */
    public function getCleanupQuantity()
    {
        return $this->cleanupQuantity;
    }

    /**
     * Sets the cleanupQuantity
     *
     * @param int $cleanupQuantity
     * @return void
     */
    public function setcleanupQuantity($cleanupQuantity)
    {
        $this->cleanupQuantity = $cleanupQuantity;
    }

    /**
     * Returns the cleanupLocalName
     *
     * @return string $cleanupLocalName
     */
    public function getCleanupLocalName()
    {
        return $this->cleanupLocalName;
    }

    /**
     * Sets the cleanupLocalName
     *
     * @param string $cleanupLocalName
     * @return void
     */
    public function setCleanupLocalName($cleanupLocalName)
    {
        $this->cleanupLocalName = $cleanupLocalName;
    }

    /**
     * Returns the cleanupLocalValue
     *
     * @return string $cleanupLocalValue
     */
    public function getCleanupLocalValue()
    {
        return $this->cleanupLocalValue;
    }

    /**
     * Sets the cleanupLocalValue
     *
     * @param string $cleanupLocalValue
     * @return void
     */
    public function setCleanupLocalValue($cleanupLocalValue)
    {
        $this->cleanupLocalValue = $cleanupLocalValue;
    }

    /**
     * Returns the cleanupServerName
     *
     * @return string $cleanupServerName
     */
    public function getCleanupServerName()
    {
        return $this->cleanupServerName;
    }

    /**
     * Sets the cleanupServerName
     *
     * @param string $cleanupServerName
     * @return void
     */
    public function setCleanupServerName($cleanupServerName)
    {
        $this->cleanupServerName = $cleanupServerName;
    }

    /**
     * Returns the cleanupServerValue
     *
     * @return string $cleanupServerValue
     */
    public function getCleanupServerValue()
    {
        return $this->cleanupServerValue;
    }

    /**
     * Sets the cleanupServerValue
     *
     * @param string $cleanupServerValue
     * @return void
     */
    public function setCleanupServerValue($cleanupServerValue)
    {
        $this->cleanupServerValue = $cleanupServerValue;
    }

    /**
     * Returns the quickSetupWizard
     *
     * @return int $quickSetupWizard
     */
    public function getQuickSetupWizard()
    {
        return $this->quickSetupWizard;
    }

    /**
     * Sets the quickSetupWizard
     *
     * @param int $quickSetupWizard
     * @return void
     */
    public function setQuickSetupWizard($quickSetupWizard)
    {
        $this->quickSetupWizard = $quickSetupWizard;
    }

    /**
     * Returns the defaultServer
     *
     * @return string $defaultServer
     */
    public function getDefaultServer()
    {
        return $this->defaultServer;
    }

    /**
     * Sets the defaultServer
     *
     * @param string $defaultServer
     * @return void
     */
    public function setDefaultServer($defaultServer)
    {
        $this->defaultServer = $defaultServer;
    }

    /**
     * Returns the cleanup
     *
     * @return string $cleanup
     */
    public function getCleanup()
    {
        return $this->cleanup;
    }

    /**
     * Sets the cleanup
     *
     * @param string $cleanup
     * @return void
     */
    public function setCleanup($cleanup)
    {
        $this->cleanup = $cleanup;
    }
}
