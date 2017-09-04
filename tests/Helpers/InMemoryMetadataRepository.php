<?php


namespace RstGroup\ZfGrafanaModule\Tests\Helpers;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;
use Webmozart\Assert\Assert;

class InMemoryMetadataRepository implements DashboardMetadataRepository
{
    private $metadata = [];

    public function __construct(array $initialMetadata = [])
    {
        // make sure it's associative array of metadata instances
        Assert::allString(array_keys($initialMetadata));
        Assert::allIsInstanceOf($initialMetadata, DashboardMetadata::class);

        $this->metadata = $initialMetadata;
    }

    /**
     * @param DashboardMetadata $metadata
     * @return void
     */
    public function saveMetadata(DashboardMetadata $metadata)
    {
        $this->metadata[$metadata->getDashboardId()->getId()] = $metadata;
    }

    /**
     * @param DashboardId $remoteId
     * @return DashboardMetadata|null
     */
    public function fetchMetadata(DashboardId $remoteId)
    {
        return isset($this->metadata[$remoteId->getId()]) ?
            $this->metadata[$remoteId->getId()] :
            null;
    }
}
