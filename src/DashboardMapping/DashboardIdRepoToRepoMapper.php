<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

interface DashboardIdRepoToRepoMapper
{
    /**
     * @param DashboardId $id
     * @return string|null
     */
    public function mapToId(DashboardId $id);
}
