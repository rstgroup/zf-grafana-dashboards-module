<?php


namespace RstGroup\ZfGrafanaModule\Grafana\Mapper;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;

final class GetDashboardResponseBodyToDashboardMapper
{
    /**
     * @param ResponseInterface $response
     * @return Dashboard
     */
    public static function mapSuccessful(ResponseInterface $response)
    {
        $body         = (string)$response->getBody();
        $responseJson = json_decode($body, true);

        $slug       = new DashboardSlug($responseJson['meta']['slug']);
        $definition = DashboardDefinition::createFromArray($responseJson['dashboard']);

        return new Dashboard($definition, $slug);
    }
}
