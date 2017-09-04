<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;

/**
 * This ID is not a part of Dashboard in Grafana API, it's an identifier used by your app to distinguish
 * between dashboard definitions.
 */
interface DashboardId
{
    /** @return string */
    public function getId();

    /**
     * @param string $id
     */
    public function __construct($id);
}
