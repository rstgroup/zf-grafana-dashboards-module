<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;

/**
 * @codeCoverageIgnore tested by integration tests
 */
final class DbalMetadataRepository implements DashboardMetadataRepository
{
    const FIELD_DASHBOARD_ID = 'dashboard_id';
    const FIELD_GRAFANA_ID = 'grafana_id';
    const FIELD_DASHBOARD_VERSION = 'dashboard_version';
    const FIELD_DASHBOARD_SCHEMA_VERSION = 'dashboard_schema_version';

    /** @var Connection */
    private $db;

    /** @var string */
    private $table;

    /**
     * @param Connection $db
     * @param string     $table
     */
    public function __construct(Connection $db, $table)
    {
        $this->db    = $db;
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function saveMetadata(DashboardMetadata $metadata)
    {
        $insertStatement = $this->db->prepare(sprintf(
            'INSERT INTO %s (`%s`, `%s`, `%s`, `%s`) VALUES (:d_id, :g_id, :d_version, :d_schema_version)',
            $this->table,
            self::FIELD_DASHBOARD_ID,
            self::FIELD_GRAFANA_ID,
            self::FIELD_DASHBOARD_VERSION,
            self::FIELD_DASHBOARD_SCHEMA_VERSION
        ));

        if (!$insertStatement->execute([
            'd_id'             => $metadata->getDashboardId()->getId(),
            'g_id'             => $metadata->getGrafanaId(),
            'd_version'        => $metadata->getVersion(),
            'd_schema_version' => $metadata->getSchemaVersion(),
        ])
        ) {
            throw new \RuntimeException(sprintf("Could not save to DB. Reason: %s", implode("\n", $insertStatement->errorInfo())));
        }
    }

    /**
     * @param DashboardId $remoteId
     * @return DashboardMetadata|null
     */
    public function fetchMetadata(DashboardId $remoteId)
    {

        $queryStatement = $this->db->prepare(sprintf(
            'SELECT * FROM %s WHERE `%s` = :ld_id',
            $this->table,
            self::FIELD_DASHBOARD_ID
        ));


        $queryStatement->execute(['ld_id' => $remoteId->getId()]);

        if ($queryStatement->rowCount()) {
            $row = $queryStatement->fetch(\PDO::FETCH_ASSOC);

            return new DashboardMetadata(
                $remoteId, (int)$row[self::FIELD_DASHBOARD_VERSION], (int)$row[self::FIELD_GRAFANA_ID], (int)$row[self::FIELD_DASHBOARD_SCHEMA_VERSION] ?: null
            );
        } else {
            return null;
        }
    }
}
