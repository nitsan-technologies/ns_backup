<?php

namespace NITSAN\NsBackup\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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
     * @param string $type
     * @return QueryBuilder|Connection
     */
    protected function getQueryBuilder(string $type = ''): QueryBuilder|Connection
    {
        if($type == 'queryBuilder') {
            return GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tx_nsbackup_domain_model_backupdata');
        } else {
            return GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_nsbackup_domain_model_backupdata');
        }
    }

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

        $queryBuilder = $this->getQueryBuilder('queryBuilder');
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
        $queryBuilder = $this->getQueryBuilder();
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
     * @param $uid
     * @return false|mixed[]
     * @throws Exception
     */
    public function findBackupByUid($uid): array|bool
    {
        $queryBuilder = $this->getQueryBuilder('queryBuilder');
        $statement = $queryBuilder
            ->select('*')
            ->from('tx_nsbackup_domain_model_backupdata')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            )
            ->executeQuery();

        return $statement->fetchAssociative();
    }

    /**
     * removeBackupData
     * @param int $uid
     */
    public function removeBackupData(int $uid): void
    {
        $queryBuilder = $this->getQueryBuilder('tx_nsbackup_domain_model_backupdata');
        $queryBuilder->delete(
            'tx_nsbackup_domain_model_backupdata', // from
            [ 'uid' => $uid ] // where
        );
    }
}
