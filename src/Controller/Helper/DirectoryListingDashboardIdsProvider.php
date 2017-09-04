<?php


namespace RstGroup\ZfGrafanaModule\Controller\Helper;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\InnerId\DashboardFilename;
use Webmozart\Assert\Assert;

final class DirectoryListingDashboardIdsProvider implements DashboardIdsProvider
{
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        Assert::directory($directory);

        $this->directory = $directory;
    }


    /** @return DashboardId[] */
    public function getDashboardIds()
    {
        return array_filter(array_map(function ($filename) {
            try {
                return new DashboardFilename($filename);
            } catch (\InvalidArgumentException $ex) {
                return null;
            }
        }, scandir($this->directory)));
    }
}
