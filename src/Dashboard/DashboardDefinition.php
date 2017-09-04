<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use Webmozart\Assert\Assert;

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
     * @param DashboardMetadata $metadata
     * @return DashboardDefinition
     */
    public function withMetadata(DashboardMetadata $metadata)
    {
        return self::createFromArray(array_replace(
            $this->decodedDefinition,
            [
                'id'            => $metadata->getGrafanaId(),
                'version'       => $metadata->getVersion(),
                'schemaVersion' => $metadata->getSchemaVersion(),
            ]
        ));
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

    /**
     * @param self $otherDefinition
     * @return bool
     */
    public function isEqual(self $otherDefinition)
    {
        try {
            Assert::eq(
                array_diff_key(
                    $this->decodedDefinition,
                    ['id' => null, 'version' => null, 'schemaVersion' => null,]
                ),
                array_diff_key(
                    $otherDefinition->decodedDefinition,
                    ['id' => null, 'version' => null, 'schemaVersion' => null,]
                )
            );

            return true;
        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }
}
