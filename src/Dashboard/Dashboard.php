<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


final class Dashboard
{
    /** @var DashboardSlug */
    private $slug;
    /** @var  DashboardDefinition */
    private $definition;
    /** @var  DashboardId|null */
    private $id;

    /**
     * @param DashboardSlug       $slug
     * @param DashboardDefinition $definition
     * @param null|DashboardId    $id
     */
    public function __construct(DashboardSlug $slug, DashboardDefinition $definition, DashboardId $id = null)
    {
        $this->slug       = $slug;
        $this->definition = $definition;
        $this->id         = $id;
    }

    /**
     * @param Dashboard   $dashboard
     * @param DashboardId $id
     * @return Dashboard
     */
    public static function fromSavedDashboard(Dashboard $dashboard, DashboardId $id)
    {
        return new Dashboard(
            $dashboard->getSlug(),
            $dashboard->getDefinition(),
            $dashboard->getId()
        );
    }

    /**
     * @return DashboardSlug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return DashboardDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return null|DashboardId
     */
    public function getId()
    {
        return $this->id;
    }
}
