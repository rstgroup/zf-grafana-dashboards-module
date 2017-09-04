<?php


namespace RstGroup\ZfGrafanaModule\Dashboard\InnerId;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use Webmozart\Assert\Assert;

final class DashboardFilename implements DashboardId
{
    /** @var string */
    private $filename;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $ext = substr($filename, -5, 5);

        Assert::same($ext, '.json', "Given file doesn't have .json extension.");

        $this->filename = $filename;
    }

    /**
     * @param string $path
     * @return self
     */
    public static function createFromPath($path)
    {
        $filename = pathinfo($path, PATHINFO_BASENAME);

        return new self($filename);
    }

    /** @return string */
    public function getId()
    {
        return $this->filename;
    }
}
