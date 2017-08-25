<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


final class Dashboard
{
    /** @var DashboardSlug */
    private $slug;
    /** @var DashboardDefinition */
    private $definition;
    /** @var  DashboardId|null */
    private $id;

    /**
     * @param DashboardDefinition $definition
     * @param null|DashboardSlug  $slug
     * @param null|DashboardId    $id
     */
    public function __construct(DashboardDefinition $definition, DashboardSlug $slug = null, DashboardId $id = null)
    {
        $this->definition = $definition;
        $this->slug       = $slug;
        $this->id         = $id;
    }

    /**
     * @param Dashboard     $dashboard
     * @param DashboardSlug $slug
     * @param DashboardId   $id
     * @return Dashboard
     */
    public static function fromSavedDashboard(Dashboard $dashboard, DashboardSlug $slug, DashboardId $id)
    {
        return new Dashboard(
            $dashboard->getDefinition(),
            $slug,
            $id
        );
    }

    /**
     * @return DashboardSlug|null
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
