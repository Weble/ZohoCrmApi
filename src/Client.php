<?php

namespace Webleit\ZohoCrmApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Weble\ZohoClient\Enums\Region;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoCrmApi\Enums\Mode;
use Webleit\ZohoCrmApi\Exception\ApiError;
use Webleit\ZohoCrmApi\Request\ListParameters;

class Client
{
    const ZOHOCRM_API_URL_PATH = "/crm/v2/";
    const ZOHOCRM_API_PRODUCION_PARTIAL_HOST = "https://www.zohoapis";
    const ZOHOCRM_API_DEVELOPER_PARTIAL_HOST = "https://developer.zoho";
    const ZOHOCRM_API_SANDBOX_PARTIAL_HOST = "https://crmsandbox.zoho";

    /**
     * @var bool
     */
    protected $retriedRefresh = false;

    /**
     * @var bool
     */
    protected $throttle = false;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var OAuthClient
     */
    protected $oAuthClient;

    /**
     * @var Mode
     */
    protected $mode;

    public function __construct(OAuthClient $oAuthClient, ClientInterface $client = null)
    {
        if (!$client) {
            $client = new \GuzzleHttp\Client();
        }

        $this->client = $client;
        $this->oAuthClient = $oAuthClient;

        $this->setMode(Mode::production());
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([
            $this->oAuthClient,
            $name,
        ], $arguments);
    }

    public function getOAuthClient(): OAuthClient
    {
        return $this->oAuthClient;
    }

    public function setOAuthClient(OAuthClient $authClient): self
    {
        $this->oAuthClient = $authClient;

        return $this;
    }

    public function setRegion(Region $region): self
    {
        $this->oAuthClient->setRegion($region);

        return $this;
    }

    public function getList(string $uri, array $params = []): array
    {
        $params = new ListParameters($params);
        $response = $this->call($uri, 'GET', ['query' => $params->toArray()]);

        $body = $response->getBody();

        $data = json_decode($body, true);

        return $data ?? [];
    }

    public function call(string $uri, string $method, array $data = []): ResponseInterface
    {
        $options = array_merge([
            'query' => [],
            'form_params' => [],
            'json' => [],
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $this->oAuthClient->getAccessToken(),
            ],
        ], $data);

        try {
            $response = $this->client->$method($this->getUrl() . $uri, array_filter($options));

            ApiError::throwFromResponse($response);

            return $response;
        } catch (ClientException $e) {
            // Retry?
            if ($e->getCode() === 401 && !$this->retriedRefresh) {
                $this->oAuthClient->generateTokens()->getAccessToken();
                $this->retriedRefresh = true;

                return $this->call($uri, $method, $data);
            }

            $response = $e->getResponse();

            if (!$response) {
                throw $e;
            }

            ApiError::throwFromResponse($response);

            return $response;
        }
    }

    public function getBaseUrl(): string
    {
        switch ($this->getMode()) {
            case Mode::developer():
                $apiUrl = self::ZOHOCRM_API_DEVELOPER_PARTIAL_HOST;

                break;
            case Mode::sandbox():
                $apiUrl = self::ZOHOCRM_API_SANDBOX_PARTIAL_HOST;

                break;
            case Mode::production():
            default:
                $apiUrl = self::ZOHOCRM_API_PRODUCION_PARTIAL_HOST;

                break;
        }

        return $apiUrl . $this->getRegion()->getValue();
    }

    public function getUrl(): string
    {
        return $this->getBaseUrl() . self::ZOHOCRM_API_URL_PATH;
    }

    public function getMode(): Mode
    {
        return $this->mode;
    }

    public function setMode(Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getRegion(): Region
    {
        return $this->oAuthClient->getRegion();
    }

    public function get(string $url, string $id = null, array $params = [])
    {
        if ($id !== null) {
            $url .= '/' . $id;
        }

        $result = $this->call($url, 'GET', ['query' => $params]);

        return $this->processResult($result);
    }

    public function post(string $url, array $params = [], array $queryParams = [])
    {
        return $this->processResult($this->call($url, 'POST', [
            'query' => $queryParams,
            'json' => $params,
        ]));
    }

    public function put(string $url, array $params = [], array $queryParams = [])
    {
        return $this->processResult($this->call($url, 'PUT', [
            'query' => $queryParams,
            'json' => $params,
        ]));
    }

    public function delete(string $url, $id)
    {
        return $this->processResult($this->call($url . '/' . $id, 'DELETE'));
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }

    public function processResult(ResponseInterface $response)
    {
        if (!$this->isSuccessfulResponse($response)) {
            ApiError::throwFromResponse($response);
        }

        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            return $this->getResponseContent($response);
        }

        if (!$result) {
            return $this->getResponseContent($response);
        }

        return $result;
    }

    protected function isSuccessfulResponse(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    protected function getResponseContent(ResponseInterface $response): string
    {
        $body = (string)$response->getBody();

        // If response is not a json, it's probably a PDF or a binary content.
        if (strlen($body) > 0) {
            return $body;
        }

        ApiError::throwFromResponse($response);
    }
}
