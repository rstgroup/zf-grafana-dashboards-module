<?php


namespace RstGroup\ZfGrafanaModule\Grafana;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\ErrorousResponseToExceptionMessageMapper;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\GetDashboardResponseBodyMapper;


final class ResponseHelper
{
    /**
     * @param ResponseInterface $response
     * @return Dashboard
     */
    public function parseResponseForGettingDashboard(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 200) {
            return GetDashboardResponseBodyMapper::mapSuccessful($response);
        }

        if ($exception = ErrorousResponseToExceptionMessageMapper::mapToException($response)) {
            throw $exception;
        }
    }
}
