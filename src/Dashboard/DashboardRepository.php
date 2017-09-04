<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;

interface DashboardRepository
{
    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function save(Dashboard $dashboard);

    /**
     * @param DashboardId $id
     * @return Dashboard
     */
    public function load(DashboardId $id);

    /**
     * @return DashboardToDashboardMapper
     */
    public function getMapper();
}
