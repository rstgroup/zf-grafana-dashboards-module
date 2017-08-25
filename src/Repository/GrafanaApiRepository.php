<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use Http\Client\HttpClient;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardRepository;

final class GrafanaApiRepository implements DashboardRepository
{
    /** @var HttpClient */
    private $httpClient;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function save(Dashboard $dashboard)
    {
        // TODO: Implement save() method.
    }

    /**
     * @param DashboardId $id
     * @return Dashboard
     */
    public function load(DashboardId $id)
    {
        /** @var \Psr\Http\Message\ResponseInterface */
        $request;
        $response = $this->httpClient->sendRequest($request);

        return $dashboard;
    }
}
