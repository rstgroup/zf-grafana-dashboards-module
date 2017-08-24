<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


final class DashboardId
{
    /** @var int */
    private $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        if (!is_int($id) || $id < 1) {
            throw new \InvalidArgumentException("Given value is not a positive integer.");
        }

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
