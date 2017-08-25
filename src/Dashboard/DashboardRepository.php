<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


interface DashboardRepository
{
    public function save(Dashboard $dashboard);

    /**
     * @param DashboardId $id
     * @return Dashboard
     */
    public function load(DashboardId $id);
}
