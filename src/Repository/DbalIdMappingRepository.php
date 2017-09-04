<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use Doctrine\DBAL\Driver\Connection;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdRepoToRepoMapper;

final class DbalIdMappingRepository implements DashboardIdRepoToRepoMapper
{
    const FIELD_LOCAL_ID = 'local_id';
    const FIELD_REMOTE_ID = 'remote_id';

    /** @var Connection */
    private $connection;
    /** @var  string */
    private $table;

    /**
     * @param Connection $connection
     * @param string     $table
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table      = $table;
    }

    public function saveMapping(DashboardId $sourceId, DashboardId $targetId)
    {
        $insertStatement = $this->connection->prepare(sprintf(
            'REPLACE INTO `%s` (`%s`, `%s`) VALUES (:local_id, :remote_id)',
            $this->table,
            self::FIELD_LOCAL_ID,
            self::FIELD_REMOTE_ID
        ));

        $result = $insertStatement->execute([
            'local_id'  => $sourceId->getId(),
            'target_id' => $targetId->getId(),
        ]);

        if (!$result) {
            throw new \RuntimeException('Could not save to DB.');
        }
    }

    /**
     * @param DashboardId $id
     * @return null|string
     */
    public function mapToId(DashboardId $id)
    {
        $queryStatement = $this->connection->prepare(sprintf(
            'SELECT `%s` FROM `%s` WHERE `%s` = :l_id LIMIT 1',
            self::FIELD_REMOTE_ID,
            $this->table,
            self::FIELD_REMOTE_ID
        ));

        $queryStatement->execute(['l_id' => $id->getId()]);

        if ($queryStatement->rowCount() > 0) {
            $row = $queryStatement->fetch(\PDO::FETCH_ASSOC);

            return $row[self::FIELD_REMOTE_ID];
        } else {
            return null;
        }
    }
}
