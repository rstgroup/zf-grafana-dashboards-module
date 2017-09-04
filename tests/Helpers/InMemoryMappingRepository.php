<?php


namespace RstGroup\ZfGrafanaModule\Tests\Helpers;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdMappingRepository;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdRepoToRepoMapper;
use Webmozart\Assert\Assert;

class InMemoryMappingRepository implements DashboardIdMappingRepository, DashboardIdRepoToRepoMapper
{
    private $mapping = [];

    public function __construct(array $initialMapping = [])
    {
        // make sure it's associative array of DashboardId instances
        Assert::allString(array_keys($initialMapping));
        Assert::allString($initialMapping);

        $this->mapping = $initialMapping;
    }

    public function saveMapping(DashboardId $sourceId, DashboardId $targetId)
    {
        $this->mapping[$sourceId->getId()] = $targetId;
    }

    /**
     * @param DashboardId $id
     * @return DashboardId|null
     */
    public function mapToId(DashboardId $id)
    {
        return isset($this->mapping[$id->getId()]) ?
            $this->mapping[$id->getId()] :
            null;
    }
}
