<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use Http\Client\HttpClient;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardRepository;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;
use RstGroup\ZfGrafanaModule\Grafana\RequestHelper;
use RstGroup\ZfGrafanaModule\Grafana\ResponseHelper;
use Webmozart\Assert\Assert;

final class GrafanaApiRepository implements DashboardRepository
{
    /** @var HttpClient */
    private $httpClient;

    private $responseHelper;
    private $requestHelper;

    private $dashboardToDashboardMapper;

    /**
     * @param HttpClient                 $httpClient
     * @param RequestHelper              $requestHelper
     * @param ResponseHelper             $responseHelper
     * @param DashboardToDashboardMapper $dashboardToDashboardMapper
     */
    public function __construct(
        HttpClient $httpClient,
        RequestHelper $requestHelper,
        ResponseHelper $responseHelper,
        DashboardToDashboardMapper $dashboardToDashboardMapper
    )
    {
        $this->httpClient                 = $httpClient;
        $this->requestHelper              = $requestHelper;
        $this->responseHelper             = $responseHelper;
        $this->dashboardToDashboardMapper = $dashboardToDashboardMapper;
    }

    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function saveDashboard(Dashboard $dashboard)
    {
        $request  = $this->requestHelper->createCreateOrUpdateDashboardRequest($dashboard);
        $response = $this->httpClient->sendRequest($request);

        $savedDashboard = $this->responseHelper->parseResponseForCreatingDashboard($response, $dashboard);

        return $this->loadDashboard($savedDashboard->getId());
    }

    /**
     * @param DashboardId $id
     * @return Dashboard
     */
    public function loadDashboard(DashboardId $id)
    {
        Assert::isInstanceOf($id, DashboardSlug::class);

        $request  = $this->requestHelper->createGetDashboardRequest($id);
        $response = $this->httpClient->sendRequest($request);

        return $this->responseHelper->parseResponseForGettingDashboard($response);
    }

    /**
     * @return DashboardToDashboardMapper
     */
    public function getDashboardMapper()
    {
        return $this->dashboardToDashboardMapper;
    }
}
