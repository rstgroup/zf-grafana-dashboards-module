<?php


namespace RstGroup\ZfGrafanaModule\Grafana;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\CreateDashboardResponseBodyToDashboardMapper;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\ErrorousResponseToExceptionMessageMapper;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\GetDashboardResponseBodyToDashboardMapper;


final class ResponseHelper
{
    /**
     * @param ResponseInterface $response
     * @return Dashboard
     */
    public function parseResponseForGettingDashboard(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 200) {
            return GetDashboardResponseBodyToDashboardMapper::mapSuccessful($response);
        }

        if ($exception = ErrorousResponseToExceptionMessageMapper::mapToException($response)) {
            throw $exception;
        }
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public function parseResponseForDeletingDashboard(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 200) {
            return true;
        }

        if ($exception = ErrorousResponseToExceptionMessageMapper::mapToException($response)) {
            throw $exception;
        }
    }

    /**
     * @param ResponseInterface $response
     * @return Dashboard
     */
    public function parseResponseForCreatingDashboard(ResponseInterface $response, Dashboard $dashboard)
    {
        if ($response->getStatusCode() === 200) {
            // decode response
            $mapper = new CreateDashboardResponseBodyToDashboardMapper($response);

            // get slug & version from response
            $slug = $mapper->getSlug();
            $version = $mapper->getVersion();

            // update definition with version
            $definition = $dashboard->getDefinition()->getDecodedDefinition();
            $definition['version'] = $version;

            return new Dashboard(
                DashboardDefinition::createFromArray($definition),
                $slug
            );
        }
    }
}
