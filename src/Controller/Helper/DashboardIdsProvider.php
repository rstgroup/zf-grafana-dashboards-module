<?php


namespace RstGroup\ZfGrafanaModule\Controller\Helper;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

interface DashboardIdsProvider
{
    /** @return DashboardId[] */
    public function getDashboardIds();
}
