<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

interface DashboardIdMappingRepository
{
    public function saveMapping(DashboardId $sourceId, DashboardId $targetId);
}
