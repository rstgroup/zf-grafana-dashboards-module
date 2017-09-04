<?php


namespace RstGroup\ZfGrafanaModule\Grafana\Mapper;


use Psr\Http\Message\ResponseInterface;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use Webmozart\Assert\Assert;

final class CreateDashboardResponseBodyToDashboardMapper
{
    private $decodedResponseBody;

    public function __construct(ResponseInterface $response)
    {
        $this->decodedResponseBody = json_decode((string)$response->getBody(), true);

        if (json_last_error()) {
            throw new \InvalidArgumentException("Invalid JSON! " . json_last_error_msg());
        }

        Assert::isArray($this->decodedResponseBody);
    }

    /**
     * @return DashboardSlug
     */
    public function getSlug()
    {
        Assert::keyExists($this->decodedResponseBody, 'slug');

        return new DashboardSlug($this->decodedResponseBody['slug']);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        Assert::keyExists($this->decodedResponseBody, 'version');

        return (int)$this->decodedResponseBody['version'];
    }
}
