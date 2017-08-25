<?php


namespace RstGroup\ZfGrafanaModule\Grafana\Mapper;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
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


        $id         = new DashboardId($responseJson['dashboard']['id']);
        $slug       = new DashboardSlug($responseJson['meta']['slug']);
        $definition = DashboardDefinition::createFromArray(
            array_diff_key(
                $responseJson['dashboard'],
                array_fill_keys(['id'], null)
            )
        );

        return new Dashboard($definition, $slug, $id);
    }
}
