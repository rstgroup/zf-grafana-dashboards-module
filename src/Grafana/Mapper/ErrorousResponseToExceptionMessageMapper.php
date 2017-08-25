<?php


namespace RstGroup\ZfGrafanaModule\Grafana\Mapper;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardAccessDenied;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardAlreadyExists;
use RstGroup\ZfGrafanaModule\Grafana\Exception\DashboardNotFound;
use RstGroup\ZfGrafanaModule\Grafana\Exception\GrafanaServerError;
use RstGroup\ZfGrafanaModule\Grafana\Exception\InvalidApiRequest;
use Webmozart\Assert\Assert;

final class ErrorousResponseToExceptionMessageMapper
{
    /**
     * @param ResponseInterface $response
     * @return \Exception|null
     */
    public static function mapToException(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        try {
            $message = self::fetchMessageFromResponse($response);
        } catch (\InvalidArgumentException $ex) {
            $message = '';
        }

        // specific situations
        switch ($statusCode) {
            case 404:
                return new DashboardNotFound($message);
            case 403:
                return new DashboardAccessDenied($message);
            case 412:
                return new DashboardAlreadyExists($message);
        }

        // general server error
        if ($statusCode >= 500) {
            return new GrafanaServerError($message);
        }

        // general request error
        if ($statusCode >= 400) {
            return new InvalidApiRequest($message);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return string
     * @throws \Exception
     */
    private static function fetchMessageFromResponse(ResponseInterface $response)
    {
        $responseJson = json_decode((string)$response->getBody(), true);

        Assert::isArray($responseJson);
        Assert::keyExists($responseJson, 'message');

        return $responseJson['message'];
    }
}
