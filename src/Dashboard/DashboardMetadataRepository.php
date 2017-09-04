<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;

interface DashboardMetadataRepository
{
    /**
     * @param DashboardMetadata $metadata
     * @return void
     */
    public function saveMetadata(DashboardMetadata $metadata);

    /**
     * @param DashboardId $remoteId
     * @return DashboardMetadata|null
     */
    public function fetchMetadata(DashboardId $remoteId);
}
