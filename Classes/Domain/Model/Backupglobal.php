<?php
namespace NITSAN\NsBackup\Domain\Model;

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
class Backupglobal extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
