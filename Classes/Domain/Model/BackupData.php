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
 * BackupData
 */
class BackupData extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected string $title = '';

    /**
     * category
     *
     * @var string
     */
    protected string $category = '';

    /**
     * startDatetime
     *
     * @var string
     */
    protected string $startDatetime = '';

    /**
     * endDatetime
     *
     * @var string
     */
    protected string $endDatetime = '';

    /**
     * backupType
     *
     * @var string
     */
    protected string $backupType = '';

    /**
     * downloadUrl
     *
     * @var string
     */
    protected string $downloadUrl = '';


    /**
     * serverUid
     *
     * @var string
     */
    protected string $serverUid = '';

    /**
     * logsUid
     *
     * @var string
     */
    protected string $logsUid = '';

    /**
     * localBackupPath
     *
     * @var string
     */
    protected string $localBackupPath = '';

    /**
     * serversBackupPath
     *
     * @var string
     */
    protected string $serversBackupPath = '';

    /**
     * filenames
     *
     * @var string
     */
    protected string $filenames = '';

    /**
     * size
     *
     * @var string
     */
    protected string $size = '';

    /**
     * logs
     *
     * @var string
     */
    protected string $logs = '';

    /**
     * date
     *
     * @var string
     */
    protected string $date = '';

    /**
     * status
     *
     * @var string
     */
    protected string $status = '';

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Returns the category
     *
     * @return string $category
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Sets the category
     *
     * @param string $category
     * @return void
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * Returns the startDatetime
     *
     * @return string $startDatetime
     */
    public function getStartDatetime(): string
    {
        return $this->startDatetime;
    }

    /**
     * Sets the startDatetime
     *
     * @param string $startDatetime
     * @return void
     */
    public function setStartDatetime(string $startDatetime): void
    {
        $this->startDatetime = $startDatetime;
    }

    /**
     * Returns the endDatetime
     *
     * @return string $endDatetime
     */
    public function getEndDatetime(): string
    {
        return $this->endDatetime;
    }

    /**
     * Sets the endDatetime
     *
     * @param string $endDatetime
     * @return void
     */
    public function setEndDatetime(string $endDatetime): void
    {
        $this->endDatetime = $endDatetime;
    }

    /**
     * Returns the backupType
     *
     * @return string $backupType
     */
    public function getBackupType(): string
    {
        return $this->backupType;
    }

    /**
     * Sets the backupType
     *
     * @param string $backupType
     * @return void
     */
    public function setBackupType(string $backupType): void
    {
        $this->backupType = $backupType;
    }

    /**
     * Returns the downloadUrl
     *
     * @return string $downloadUrl
     */
    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    /**
     * Sets the downloadUrl
     *
     * @param string $downloadUrl
     * @return void
     */
    public function setDownloadUrl(string $downloadUrl): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Returns the serverUid
     *
     * @return string $serverUid
     */
    public function getServerUid(): string
    {
        return $this->serverUid;
    }

    /**
     * Sets the serverUid
     *
     * @param string $serverUid
     * @return void
     */
    public function setServerUid(string $serverUid): void
    {
        $this->serverUid = $serverUid;
    }

    /**
     * Returns the logsUid
     *
     * @return string $logsUid
     */
    public function getLogsUid(): string
    {
        return $this->logsUid;
    }

    /**
     * Sets the logsUid
     *
     * @param string $logsUid
     * @return void
     */
    public function setLogsUid(string $logsUid): void
    {
        $this->logsUid = $logsUid;
    }

    /**
     * Returns the localBackupPath
     *
     * @return string $localBackupPath
     */
    public function getLocalBackupPath(): string
    {
        return $this->localBackupPath;
    }

    /**
     * Sets the localBackupPath
     *
     * @param string $localBackupPath
     * @return void
     */
    public function setLocalBackupPath(string $localBackupPath): void
    {
        $this->localBackupPath = $localBackupPath;
    }

    /**
     * Returns the serversBackupPath
     *
     * @return string $serversBackupPath
     */
    public function getServersBackupPath(): string
    {
        return $this->serversBackupPath;
    }

    /**
     * Sets the serversBackupPath
     *
     * @param string $serversBackupPath
     * @return void
     */
    public function setServersBackupPath(string $serversBackupPath): void
    {
        $this->serversBackupPath = $serversBackupPath;
    }

    /**
     * Returns the filenames
     *
     * @return string $filenames
     */
    public function getFilenames(): string
    {
        return $this->filenames;
    }

    /**
     * Sets the filenames
     *
     * @param string $filenames
     * @return void
     */
    public function setFilenames(string $filenames): void
    {
        $this->filenames = $filenames;
    }

    /**
     * Returns the size
     *
     * @return string $size
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * Sets the size
     *
     * @param string $size
     * @return void
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * Returns the logs
     *
     * @return string $logs
     */
    public function getLogs(): string
    {
        return $this->logs;
    }

    /**
     * Sets the logs
     *
     * @param string $logs
     * @return void
     */
    public function setLogs(string $logs): void
    {
        $this->logs = $logs;
    }

    /**
     * Returns the date
     *
     * @return string $date
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Sets the date
     *
     * @param string $date
     * @return void
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * Returns the status
     *
     * @return string $status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
