<?php


namespace RstGroup\ZfGrafanaModule\Grafana;


use Http\Message\RequestFactory;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use Psr\Http\Message\RequestInterface;
use RstGroup\ZfGrafanaModule\Grafana\Mapper\DashboardToRequestBodyMapper;

class RequestHelper
{
    /** @var RequestFactory */
    private $requestFactory;

    /** @var string */
    private $grafanaApiBaseUri;

    /** @var  string */
    private $grafanaApiKey;

    /**
     * @param RequestFactory $requestFactory
     * @param string         $grafanaApiUri
     * @param string         $grafanaApiKey
     */
    public function __construct(RequestFactory $requestFactory, $grafanaApiUri, $grafanaApiKey)
    {
        $this->requestFactory    = $requestFactory;
        $this->grafanaApiBaseUri = $grafanaApiUri;
        $this->grafanaApiKey     = $grafanaApiKey;
    }

    /**
     * @param DashboardSlug $slug
     * @return RequestInterface
     */
    public function createGetDashboardRequest(DashboardSlug $slug)
    {
        return $this->requestFactory->createRequest(
            'GET',
            $this->grafanaApiBaseUri . '/dashboards/db/' . $slug->getSlug(),
            array_replace([], $this->getAuthorizationHeaders())
        );
    }

    /**
     * @param Dashboard $dashboard
     * @return RequestInterface
     */
    public function createCreateDashboardRequest(Dashboard $dashboard)
    {
        return $this->requestFactory->createRequest(
            'POST',
            $this->grafanaApiBaseUri . '/dashboards/db',
            array_replace(
                [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
                $this->getAuthorizationHeaders()
            ),
            json_encode(DashboardToRequestBodyMapper::map($dashboard))
        );
    }

    /**
     * @param DashboardSlug $slug
     * @return RequestInterface
     */
    public function createDeleteDashboardRequest(DashboardSlug $slug)
    {
        return $this->requestFactory->createRequest(
            'DELETE',
            $this->grafanaApiBaseUri . '/dashboards/db/' . $slug->getSlug(),
            array_replace(['Accept' => 'application/json'], $this->getAuthorizationHeaders())
        );
    }

    /**
     * @return string[]
     */
    private function getAuthorizationHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->grafanaApiKey,
        ];
    }
}
