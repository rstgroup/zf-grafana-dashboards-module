<?php


namespace RstGroup\ZfGrafanaModule\Tests\Helpers;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

final class DashboardSimpleId implements DashboardId
{
    private $id;

    /** @return string */
    public function getId()
    {
        return (string)$this->id;
    }

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
