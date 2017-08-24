<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


interface DashboardRepository
{
    public function save(Dashboard $dashboard);

    /**
     * @return Dashboard
     */
    public function load();
}
