<?php
namespace NITSAN\NsBackup\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

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
class BackupglobalRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    ];

    /**
     * findBackupDataAll
     */
    public function findBackupDataAll($limit = 0) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nsbackup_domain_model_backupdata');

        if($limit > 0) {
            $statement = $queryBuilder
                ->select('*')
                ->from('tx_nsbackup_domain_model_backupdata')
                ->orderBy('uid', 'DESC')
                ->setMaxResults($limit)
                ->execute();
        }
        else {
            $statement = $queryBuilder
                ->select('*')
                ->from('tx_nsbackup_domain_model_backupdata')
                ->orderBy('uid', 'DESC')
                ->execute();
        }

        $arrReturn = array();

        while ($row = $statement->fetch()) {
            $arrReturn[] = $row;
        }

        return $arrReturn;
    }

    /**
     * addBackupData
     */
    public function addBackupData($arrData) {
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
                    //'server_uid' => $arrData['servers'],
                    'logs' => $arrData['log'],
                    'size' => $arrData['size'],
                    'jsonfile' => $arrData['jsonfile'],
                ]
            );
    }

    /**
     * findBackupByUid
     */
    public function findBackupByUid($arrUid) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nsbackup_domain_model_backupdata');
        $statement = $queryBuilder
            ->select('*')
            ->from('tx_nsbackup_domain_model_backupdata')
            ->where('uid', $arrUid[0])
            ->execute();

        $row = $statement->fetch();

        return $row;
    }

    /**
     * removeBackupData
     */
    public function removeBackupData($arrUid) {
        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_nsbackup_domain_model_backupdata')
        ->delete(
            'tx_nsbackup_domain_model_backupdata', // from
            [ 'uid' => $arrUid[0] ] // where
        );
    }
}
