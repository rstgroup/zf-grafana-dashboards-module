<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


final class DashboardDefinition
{
    /** @var string */
    private $definition;

    /**
     * @param string $definition
     */
    public function __construct($definition)
    {
        json_decode($definition);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON given: " . json_last_error_msg());
        }

        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDefinition();
    }
}
