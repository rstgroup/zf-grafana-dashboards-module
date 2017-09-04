<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;

final class DashboardSlug implements DashboardId
{
    /** @var string */
    private $slug;

    /**
     * @param string $slug
     */
    public function __construct($slug)
    {
        if (preg_match('/^[a-z0-9-]+$/', $slug) == 0) {
            throw new \InvalidArgumentException("DashboardSlug requires non-empty string matching regex: [a-z0-9-]+.");
        }

        $this->slug = $slug;
    }

    public function __toString()
    {
        return $this->slug;
    }

    /** @return string */
    public function getId()
    {
        return $this->slug;
    }
}
