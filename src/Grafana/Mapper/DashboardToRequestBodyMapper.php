<?php


namespace RstGroup\ZfGrafanaModule\Grafana\Mapper;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;

final class DashboardToRequestBodyMapper
{
    /**
     * @param Dashboard $dashboard
     * @return string
     */
    public static function map(Dashboard $dashboard)
    {
        $id = $dashboard->getId();

        return [
            'dashboard' => array_replace(
                $dashboard->getDefinition()->getDecodedDefinition(),
                [ 'id' => $id ? $id->getId() : null ]
            ),
        ];
    }
}
