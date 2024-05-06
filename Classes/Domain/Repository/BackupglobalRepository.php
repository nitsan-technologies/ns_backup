<?php

namespace NITSAN\NsBackup\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
 * The repository for Tests
 */
class BackupglobalRepository extends Repository
{
    /**
     * @var array<non-empty-string, 'ASC'|'DESC'>
     */
    protected $defaultOrderings = [
        'uid' => QueryInterface::ORDER_DESCENDING
    ];

    /**
     * findBackupDataAll
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function findBackupDataAll(int $limit = 0): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsbackup_domain_model_backupdata');
        $queryBuilder
            ->select('*')
            ->from('tx_nsbackup_domain_model_backupdata')
            ->orderBy('uid', 'DESC');
        if($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }
        $statement = $queryBuilder->executeQuery();
        $arrReturn = array();
        while ($row = $statement->fetchAssociative()) {
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }

    /**
     * addBackupData
     * @param array $arrData
     */
    public function addBackupData(array $arrData): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_nsbackup_domain_model_backupdata');
        $queryBuilder
            ->insert(
                'tx_nsbackup_domain_model_backupdata',
                [
                    'title' => $arrData['backupName'],
                    'start_datetime' => strtotime("now"),
                    'backup_type' => $arrData['backupFolderSettings'],
                    'download_url' => $arrData['download_url'],
                    'filenames' => $arrData['filenames'],
                    'logs' => $arrData['log'],
                    'size' => $arrData['size'],
                    'jsonfile' => $arrData['jsonfile'],
                ]
            );
    }

    /**
     * findBackupByUid
     * @param int $uid
     * @return false|mixed[]
     * @throws Exception
     */
    public function findBackupByUid(int $uid): array|bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsbackup_domain_model_backupdata');
        $statement = $queryBuilder
            ->select('*')
            ->from('tx_nsbackup_domain_model_backupdata')
            ->where('uid', $uid)
            ->executeQuery();

        return $statement->fetchAssociative();
    }

    /**
     * removeBackupData
     * @param int $uid
     */
    public function removeBackupData(int $uid): void
    {
        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_nsbackup_domain_model_backupdata')
        ->delete(
            'tx_nsbackup_domain_model_backupdata', // from
            [ 'uid' => $uid ] // where
        );
    }
}
