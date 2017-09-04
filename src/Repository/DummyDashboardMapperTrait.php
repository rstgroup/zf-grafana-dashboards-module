<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;
use RstGroup\ZfGrafanaModule\Repository\Mapper\DummyDashboardMapper;

/**
 * @codeCoverageIgnore
 */
trait DummyDashboardMapperTrait
{
    private $dummyMapper;

    /**
     * @return DashboardToDashboardMapper
     */
    public function getMapper()
    {
        if (!$this->dummyMapper) {
            $this->dummyMapper = new DummyDashboardMapper();
        }

        return $this->dummyMapper;
    }
}
