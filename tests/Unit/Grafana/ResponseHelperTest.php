<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Grafana;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardAccessDenied;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardAlreadyExists;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardNotFound;
use RstGroup\ZfGrafanaModule\Grafana\Exception\GrafanaServerError;
use RstGroup\ZfGrafanaModule\Grafana\Exception\InvalidApiRequest;
use RstGroup\ZfGrafanaModule\Grafana\ResponseHelper;
use PHPUnit\Framework\TestCase;

class ResponseHelperTest extends TestCase
{
    /** @var ResponseHelper */
    private $helper;

    public function setUp()
    {
        $this->helper = new ResponseHelper();
    }

    public function testItFetchesDashboardFromSuccessfulResponse()
    {
        // given: response stub
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            '{"meta":{"isStarred":false,"slug":"test-title"},"dashboard":{"id":123,"title":"Test title","tags":["templated"],"timezone":"browser","rows":[],"schemaVersion":6,"version":0}}'
        );

        // when
        $dashboard = $this->helper->parseResponseForGettingDashboard($response);

        // then: return value is a Dashboard instance
        $this->assertInstanceOf(Dashboard::class, $dashboard);

        // then: the instance has values fetched from Response
        $this->assertEquals(
            ['id' => 123, 'title' => 'Test title', 'tags' => ['templated'], 'timezone' => 'browser', 'rows' => [], 'schemaVersion' => 6, 'version' => 0],
            $dashboard->getDefinition()->getDecodedDefinition()
        );
        $this->assertSame('test-title', $dashboard->getId()->getId());
    }

    /**
     * @dataProvider errorousResponsesProvider
     *
     * @param ResponseInterface $errorousResponse
     * @param string            $expectedExceptionClass
     * @param string            $expectedMessage
     */
    public function testItThrowsExceptionsOnFailureResponse(ResponseInterface $errorousResponse, $expectedExceptionClass, $expectedMessage)
    {
        // expect:
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedMessage);

        // when: response is parsed
        $this->helper->parseResponseForGettingDashboard($errorousResponse);
    }

    public function errorousResponsesProvider()
    {
        return [
            [
                new Response(403, ['Content-Type' => 'application/json'], '{"message":"Access denied"}'),
                DashboardAccessDenied::class,
                "Access denied"
            ],
            [
                new Response(404, ['Content-Type' => 'application/json'], '{"message":"Dashboard not found"}'),
                DashboardNotFound::class,
                "Dashboard not found"
            ],
            [
                new Response(412, ['Content-Type' => 'application/json'], '{"message":"Dashboard already exists"}'),
                DashboardAlreadyExists::class,
                "Dashboard already exists"
            ],
            [
                new Response(500, ['Content-Type' => 'application/json'], '{"message":"Grafana service not available"}'),
                GrafanaServerError::class,
                "Grafana service not available"
            ],
            [
                new Response(400, ['Content-Type' => 'application/json'], '{"message":"Unknown request error"}'),
                InvalidApiRequest::class,
                "Unknown request error"
            ],
            [
                new Response(400, ['Content-Type' => 'application/json'], ''),
                InvalidApiRequest::class,
                ""
            ],
        ];
    }

    public function testItReturnsCreatedDashboard()
    {
        // given: dashboard definition to save
        $sourceDashboard = new Dashboard(new DashboardDefinition('{"title":"My dashboard"}'));

        // given: API response
        $response = new Response(200, [], '{"status":"success","version":1,"slug":"my-dashboard"}');

        // when
        $savedDashboard = $this->helper->parseResponseForCreatingDashboard($response, $sourceDashboard);

        // then
        $this->assertInstanceOf(Dashboard::class, $savedDashboard);

        $this->assertInstanceOf(DashboardSlug::class, $savedDashboard->getId());
        $this->assertSame('my-dashboard', $savedDashboard->getId()->getId());

        $this->assertSame(1, $savedDashboard->getDefinition()->getDecodedDefinition()['version']);
    }
}
