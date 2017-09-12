<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;

interface DashboardRepository
{
    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function saveDashboard(Dashboard $dashboard);

    /**
     * @param DashboardId $id
     * @return Dashboard
     */
    public function loadDashboard(DashboardId $id);

    /**
     * @return DashboardToDashboardMapper
     */
    public function getDashboardMapper();
}
