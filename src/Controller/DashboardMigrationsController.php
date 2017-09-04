<?php


namespace RstGroup\ZfGrafanaModule\Controller;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardRepository;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdMappingRepository;
use Webmozart\Assert\Assert;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

final class DashboardMigrationsController extends AbstractConsoleController
{
    /** @var DashboardId[] */
    private $dashboardsIds;

    /** @var  DashboardRepository */
    private $sourceRepository;

    /** @var DashboardRepository */
    private $remoteRepository;

    /** @var DashboardMetadataRepository */
    private $metadataRepository;

    /** @var DashboardIdMappingRepository */
    private $idMappingRepository;

    /**
     * @param DashboardId[]                $dashboardsIds
     * @param DashboardRepository          $sourceRepository
     * @param DashboardRepository          $remoteRepository
     * @param DashboardMetadataRepository  $metadataRepository
     * @param DashboardIdMappingRepository $idMappingRepository
     */
    public function __construct(
        array $dashboardsIds,
        DashboardRepository $sourceRepository,
        DashboardRepository $remoteRepository,
        DashboardMetadataRepository $metadataRepository,
        $idMappingRepository
    )
    {
        // make sure valid IDs are provided
        Assert::allIsInstanceOf($dashboardsIds, DashboardId::class);

        $this->dashboardsIds                = $dashboardsIds;
        $this->sourceRepository             = $sourceRepository;
        $this->remoteRepository             = $remoteRepository;
        $this->metadataRepository           = $metadataRepository;
        $this->idMappingRepository          = $idMappingRepository;
    }


    public function migrateAction()
    {
        // 1. load dashboards to migrate
        $dashboards = array_map(function (DashboardId $id) {
            return $this->sourceRepository->load($id);
        }, $this->dashboardsIds);

        // 2. send dashboards to remote repository one by one
        array_walk($dashboards, [$this, 'migrateSingleDashboard']);
    }

    /**
     * @param Dashboard $dashboard
     */
    private function migrateSingleDashboard(Dashboard $dashboard)
    {
        $dashboardToSync = $this->remoteRepository->getMapper()->map($dashboard);
        $remoteDashboard = null;

        if ($dashboardToSync->getId() !== null) {
            $remoteDashboard = $this->remoteRepository->load($dashboardToSync->getId());
        }

        if (
            // no dashboard in remote repository ..
            $remoteDashboard === null ||
            // ..or remote repo has other definition of dashboard
            !$dashboardToSync->getDefinition()->isEqual($remoteDashboard->getDefinition())
        ) {
            // next case - need to update remote dashboard
            $updatedDashboard = $this->remoteRepository->save($dashboardToSync);

            // ..and pass updated metadata to local repository
            $metadata = DashboardMetadata::createFromDashboard($updatedDashboard);
            $this->metadataRepository->saveMetadata($metadata);

            // .. then - update local id to remote id mapping
            $this->idMappingRepository->saveMapping($dashboard->getId(), $updatedDashboard->getId());
        }
    }
}
