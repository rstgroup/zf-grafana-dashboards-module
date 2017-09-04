<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use Webmozart\Assert\Assert;

final class Dashboard
{
    /** @var DashboardDefinition */
    private $definition;
    /** @var  DashboardId|null */
    private $id;

    /**
     * @param DashboardDefinition $definition
     * @param null|DashboardId    $id
     */
    public function __construct(DashboardDefinition $definition, DashboardId $id = null)
    {
        $this->definition = $definition;
        $this->id         = $id;
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

    public function isEqual(self $dashB)
    {
        try {
            // make sure ID's are the same
            Assert::eq($this->getId(), $dashB->getId());

            // make sure definitions are the same - with exceptions :)
            return $this->getDefinition()->isEqual($dashB->getDefinition());

        } catch (\InvalidArgumentException $ex) {
            return false;
        }
    }
}
