<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;

interface DashboardToDashboardMapper
{
    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function map(Dashboard $dashboard);
}
