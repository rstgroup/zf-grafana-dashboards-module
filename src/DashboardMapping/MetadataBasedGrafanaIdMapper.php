<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;

final class MetadataBasedGrafanaIdMapper implements DashboardIdToGrafanaIdMapper
{
    /** @var DashboardMetadataRepository */
    private $repository;

    /** @var DashboardMetadata[] */
    private $localCache = [];

    /**
     * @param DashboardMetadataRepository $repository
     */
    public function __construct(DashboardMetadataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DashboardId $id
     * @return int|null
     */
    public function mapToGrafanaId(DashboardId $id)
    {
        return ( ($metadata = $this->fetchMetadata($id)) !== null) ?
            $metadata->getGrafanaId() :
            null;
    }

    private function fetchMetadata(DashboardId $id)
    {
        if (!isset($this->localCache[$id->getId()])) {
            $this->localCache[$id->getId()] = $this->repository->fetchMetadata($id);
        }

        return $this->localCache[$id->getId()];
    }
}
