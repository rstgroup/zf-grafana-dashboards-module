<?php


namespace RstGroup\ZfGrafanaModule\Repository\Mapper;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;

final class DummyDashboardMapper implements DashboardToDashboardMapper
{

    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function map(Dashboard $dashboard)
    {
        return $dashboard;
    }
}
