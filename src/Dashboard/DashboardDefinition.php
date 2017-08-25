<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


final class DashboardDefinition
{
    /** @var string */
    private $definition;

    /** @var mixed */
    private $decodedDefinition;

    /**
     * @param string $definition
     */
    public function __construct($definition)
    {
        $this->decodedDefinition = json_decode($definition, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON given: " . json_last_error_msg());
        }

        $this->definition = $definition;
    }

    /**
     * @param array $definition
     * @return DashboardDefinition
     */
    public static function createFromArray(array $definition)
    {
        $instance = new self('{}');

        $instance->definition        = json_encode($definition);
        $instance->decodedDefinition = $definition;

        return $instance;
    }

    /**
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return mixed
     */
    public function getDecodedDefinition()
    {
        return $this->decodedDefinition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDefinition();
    }
}
