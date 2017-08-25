<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Grafana;


use Http\Message\MessageFactory\GuzzleMessageFactory;
use Psr\Http\Message\RequestInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Grafana\RequestHelper;
use PHPUnit\Framework\TestCase;

class RequestHelperTest extends TestCase
{
    private $grafanaApiKey;

    private $grafanaApiUri;

    private $requestFactory;

    /** @var RequestHelper */
    private $grafanaHelper;

    public function setUp()
    {
        // given: request factory
        $this->requestFactory = new GuzzleMessageFactory();

        // given: grafana connection parameters
        $this->grafanaApiKey = 'grafana_key';
        $this->grafanaApiUri = 'http://grafana/api';

        // given: request helper
        $this->grafanaHelper = new RequestHelper($this->requestFactory, $this->grafanaApiUri, $this->grafanaApiKey);
    }

    public function testItCreatesRequestForGettingDashboard()
    {
        // given: dashboard url ID
        $slug = new DashboardSlug('dashboard-slug');

        // when: request is created
        $request = $this->grafanaHelper->createGetDashboardRequest($slug);

        // then: request is created
        $this->assertInstanceOf(RequestInterface::class, $request);

        // then: request has crucial parameters
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://grafana/api/dashboards/db/dashboard-slug', (string)$request->getUri());
        $this->assertAuthorization($this->grafanaApiKey, $request);
    }

    public function testItCreatesRequestForCreatingDashboard()
    {
        // given: dashboard
        $dashboard = new Dashboard(
            new DashboardDefinition('{"id":null,"title":"Test dashboard"}'),
            new DashboardSlug('dash')
        );

        // when: request is created
        $request = $this->grafanaHelper->createCreateDashboardRequest($dashboard);

        // then: request is created
        $this->assertInstanceOf(RequestInterface::class, $request);

        // then: request has crucial parameters
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('http://grafana/api/dashboards/db', (string)$request->getUri());
        $this->assertArraySubset(
            ['dashboard' => ['id' => null, 'title' => 'Test dashboard']],
            json_decode((string)$request->getBody(), true)
        );

        // then: request has set headers:
        $this->assertContains('application/json', $request->getHeader('Content-Type'));
        $this->assertContains('application/json', $request->getHeader('Accept'));
        $this->assertAuthorization($this->grafanaApiKey, $request);
    }

    public function testItCreatesRequestForDeletingDashboard()
    {
        // given: dashboard
        $dashboardSlug = new DashboardSlug('dash');

        // when: request is created
        $request = $this->grafanaHelper->createDeleteDashboardRequest($dashboardSlug);

        // then: request is created
        $this->assertInstanceOf(RequestInterface::class, $request);

        // then: request has crucial parameters
        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://grafana/api/dashboards/db/dash', (string)$request->getUri());

        // then: request has set headers:
        $this->assertContains('application/json', $request->getHeader('Accept'));
        $this->assertAuthorization($this->grafanaApiKey, $request);
    }
    
    private function assertAuthorization($expectedApiKey, RequestInterface $actualRequest)
    {
        $authorizationHeader = $actualRequest->getHeader('Authorization');

        $this->assertNotEmpty($authorizationHeader);
        $this->assertContains(sprintf('Bearer %s', $expectedApiKey), $authorizationHeader);
    }
}
