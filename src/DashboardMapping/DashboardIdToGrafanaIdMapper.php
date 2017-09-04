<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

interface DashboardIdToGrafanaIdMapper
{
    /**
     * @param DashboardId $id
     * @return int|null
     */
    public function mapToGrafanaId(DashboardId $id);
}
