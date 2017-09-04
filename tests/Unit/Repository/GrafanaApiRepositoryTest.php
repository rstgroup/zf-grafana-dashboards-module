<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Repository;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Psr\Http\Message\RequestInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Grafana\RequestHelper;
use RstGroup\ZfGrafanaModule\Grafana\ResponseHelper;
use RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Repository\Mapper\DummyDashboardMapper;

class GrafanaApiRepositoryTest extends TestCase
{
    /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;
    /** @var MessageFactory */
    private $messageFactory;
    /** @var  RequestHelper */
    private $grafanaRequestHelper;
    /** @var  ResponseHelper */
    private $grafanaResponseHelper;

    /** @var string */
    private $grafanaApiUri;
    /** @var string */
    private $grafanaApiKey;

    public function setUp()
    {
        // http client mock
        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)->getMock();

        // given: request factory
        $this->messageFactory = new GuzzleMessageFactory();

        // given: grafana connection parameters
        $this->grafanaApiKey = 'grafana_key';
        $this->grafanaApiUri = 'http://grafana/api';

        // given: request helper
        $this->grafanaRequestHelper = new RequestHelper($this->messageFactory, $this->grafanaApiUri, $this->grafanaApiKey);
        $this->grafanaResponseHelper = new ResponseHelper();
    }

    public function testItReturnsTheMapperPassedInConstructor()
    {
        // given: some mapper
        $mapper = new DummyDashboardMapper();

        // given: grafana repository
        $apiRepo = new GrafanaApiRepository(
            $this->httpClientMock,
            $this->grafanaRequestHelper,
            $this->grafanaResponseHelper,
            $mapper
        );

        // when
        $returnMapper = $apiRepo->getMapper();

        // then
        $this->assertSame($mapper, $returnMapper);
    }

    public function testItLoadsDashboardFromGrafanaApi()
    {
        // given: dashboard url ID
        $slug = new DashboardSlug('test-title');

        // given: grafana repository
        $apiRepo = new GrafanaApiRepository(
            $this->httpClientMock,
            $this->grafanaRequestHelper,
            $this->grafanaResponseHelper,
            new DummyDashboardMapper()
        );

        // expect: http client called
        $this->httpClientMock->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"meta":{"isStarred":false,"slug":"test-title"},"dashboard":{"id":123,"title":"Test title","tags":["templated"],"timezone":"browser","rows":[],"schemaVersion":6,"version":0}}'
            ));

        // when: request is created
        $dashboard = $apiRepo->load($slug);

        // then: dashboard is created
        $this->assertInstanceOf(Dashboard::class, $dashboard);

        // then: dashboard has crucial params
        $this->assertEquals(new Dashboard(
            new DashboardDefinition('{"id":123,"title":"Test title","tags":["templated"],"timezone":"browser","rows":[],"schemaVersion":6,"version":0}'),
            new DashboardSlug('test-title')
        ), $dashboard);
    }

    public function testItSavesDashboardViaGrafanaApi()
    {
        // given: dashboard to save
        $definitionAsJson = '{"id":null,"rows":[]}';
        $dashboard        = new Dashboard(new DashboardDefinition($definitionAsJson));

        // given: grafana repository
        $apiRepo = new GrafanaApiRepository(
            $this->httpClientMock,
            $this->grafanaRequestHelper,
            $this->grafanaResponseHelper,
            new DummyDashboardMapper()
        );

        // expect: http client called
        $this->httpClientMock->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->callback(function (RequestInterface $request) use ($definitionAsJson) {
                    // make sure it's POST,
                    $this->assertSame('POST', $request->getMethod());
                    // make sure URL is valid
                    $this->assertSame($this->grafanaApiUri . '/dashboards/db', (string)$request->getUri());
                    // make sure BODY contains given definition
                    $this->assertContains('"dashboard":' . $definitionAsJson, $request->getBody()->getContents());
                    // make sure the API KEY is passed as Authorization
                    $this->assertAuthorization($this->grafanaApiKey, $request);

                    return true;
                })
            )
            ->willReturn(new Response(
                200, [], '{"success":"success","version":1,"slug":"my-dashboard"}'
            ));

        // when: request is created
        $savedDashboard = $apiRepo->save($dashboard);

        // then: dashboard is created
        $this->assertInstanceOf(Dashboard::class, $savedDashboard);

        // then: dashboard has crucial params
        $this->assertTrue($savedDashboard->getDefinition()->isEqual(new DashboardDefinition('{"id":null,"rows":[],"version":1}')));
        $this->assertEquals(new DashboardSlug('my-dashboard'), $savedDashboard->getId());
    }

    private function assertAuthorization($expectedApiKey, RequestInterface $actualRequest)
    {
        $authorizationHeader = $actualRequest->getHeader('Authorization');

        $this->assertContains(sprintf('Bearer %s', $expectedApiKey), $authorizationHeader);
    }
}
