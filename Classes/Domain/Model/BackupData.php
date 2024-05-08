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
 * BackupData
 */
class BackupData extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * startDatetime
     *
     * @var string
     */
    protected $startDatetime = '';

    /**
     * endDatetime
     *
     * @var string
     */
    protected $endDatetime = '';

    /**
     * backupType
     *
     * @var string
     */
    protected $backupType = '';

    /**
     * downloadUrl
     *
     * @var string
     */
    protected $downloadUrl = '';

    /**
     * filenames
     *
     * @var string
     */
    protected $filenames = '';

    /**
     * size
     *
     * @var string
     */
    protected $size = '';

    /**
     * logs
     *
     * @var string
     */
    protected $logs = '';

    /**
     * date
     *
     * @var string
     */
    protected $date = '';

    /**
     * status
     *
     * @var string
     */
    protected $status = '';

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the startDatetime
     *
     * @return string $startDatetime
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Sets the startDatetime
     *
     * @param string $startDatetime
     * @return void
     */
    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;
    }

    /**
     * Returns the endDatetime
     *
     * @return string $endDatetime
     */
    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    /**
     * Sets the endDatetime
     *
     * @param string $endDatetime
     * @return void
     */
    public function setEndDatetime($endDatetime)
    {
        $this->endDatetime = $endDatetime;
    }

    /**
     * Returns the backupType
     *
     * @return string $backupType
     */
    public function getBackupType()
    {
        return $this->backupType;
    }

    /**
     * Sets the backupType
     *
     * @param string $backupType
     * @return void
     */
    public function setBackupType($backupType)
    {
        $this->backupType = $backupType;
    }

    /**
     * Returns the downloadUrl
     *
     * @return string $downloadUrl
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * Sets the downloadUrl
     *
     * @param string $downloadUrl
     * @return void
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Returns the filenames
     *
     * @return string $filenames
     */
    public function getFilenames()
    {
        return $this->filenames;
    }

    /**
     * Sets the filenames
     *
     * @param string $filenames
     * @return void
     */
    public function setFilenames($filenames)
    {
        $this->filenames = $filenames;
    }

    /**
     * Returns the size
     *
     * @return string $size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the size
     *
     * @param string $size
     * @return void
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Returns the logs
     *
     * @return string $logs
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Sets the logs
     *
     * @param string $logs
     * @return void
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

    /**
     * Returns the date
     *
     * @return string $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date
     *
     * @param string $date
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Returns the status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
