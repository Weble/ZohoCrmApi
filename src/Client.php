<?php

namespace Webleit\ZohoCrmApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Weble\ZohoClient\Enums\Region;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoCrmApi\Enums\Mode;
use Webleit\ZohoCrmApi\Exception\ApiError;
use Webleit\ZohoCrmApi\Request\ListHeaders;
use Webleit\ZohoCrmApi\Request\ListParameters;

class Client
{
    protected const ZOHOCRM_API_URL_PATH = "/crm/v2/";
    protected const ZOHOCRM_API_PRODUCION_PARTIAL_HOST = "https://www.zohoapis";
    protected const ZOHOCRM_API_DEVELOPER_PARTIAL_HOST = "https://developer.zoho";
    protected const ZOHOCRM_API_SANDBOX_PARTIAL_HOST = "https://crmsandbox.zoho";

    /**
     * @var bool
     */
    protected $retriedRefresh = false;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var OAuthClient
     */
    protected $oAuthClient;

    /**
     * @var string
     */
    protected $mode;

    public function __construct(OAuthClient $oAuthClient, ClientInterface $client = null)
    {
        if (! $client) {
            $client = new \GuzzleHttp\Client();
        }

        $this->client = $client;
        $this->oAuthClient = $oAuthClient;

        $this->setMode(Mode::PRODUCTION);
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

    public function setRegion(string $region): self
    {
        $this->oAuthClient->setRegion($region);

        return $this;
    }

    /**
     * @param string $uri
     * @param array|ListParameters $params
     * @param array|ListHeaders $headers
     * @return array
     * @throws ApiError
     * @throws Exception\AuthFailed
     * @throws Exception\DuplicateData
     * @throws Exception\InvalidData
     * @throws Exception\InvalidDataFormat
     * @throws Exception\InvalidDataType
     * @throws Exception\InvalidModule
     * @throws Exception\InvalidUrlPattern
     * @throws Exception\LimitExceeded
     * @throws Exception\MandatoryDataNotFound
     * @throws Exception\MethodNotAllowed
     * @throws Exception\OAuthScopeMismatch
     * @throws Exception\RequestEntityTooLarge
     * @throws Exception\TooManyRequests
     * @throws Exception\Unauthorized
     * @throws Exception\UnsupportedMediaType
     */
    public function getList(string $uri, $params = [], $headers = []): array
    {
        if (! $params instanceof ListParameters) {
            $params = new ListParameters($params);
        }

        if (! $headers instanceof ListHeaders) {
            $headers = new ListHeaders($headers);
        }

        $response = $this->call($uri, 'GET', ['query' => $params->toArray(), 'headers' => $headers->toArray()]);

        ApiError::throwFromResponse($response);

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
        ], $data);

        $options['headers'] = array_merge($data['headers'] ?? [], [
            'Authorization' =>
            'Zoho-oauthtoken ' . $this->oAuthClient->getAccessToken(),
        ]);

        try {
            $response = $this->client->$method($this->getUrl() . $uri, array_filter($options));

            ApiError::throwFromResponse($response);

            return $response;
        } catch (ClientException $e) {
            // Retry?
            if ($e->getCode() === 401 && ! $this->retriedRefresh) {
                $this->oAuthClient->getAccessToken();
                $this->retriedRefresh = true;

                return $this->call($uri, $method, $data);
            }

            $response = $e->getResponse();

            if (! $response) {
                throw $e;
            }

            ApiError::throwFromResponse($response);

            return $response;
        }
    }

    public function getBaseUrl(): string
    {
        switch ($this->getMode()) {
            case Mode::DEVELOPER:
                $apiUrl = self::ZOHOCRM_API_DEVELOPER_PARTIAL_HOST;

                break;
            case Mode::SANDBOX:
                $apiUrl = self::ZOHOCRM_API_SANDBOX_PARTIAL_HOST;

                break;
            case Mode::PRODUCTION:
            default:
                $apiUrl = self::ZOHOCRM_API_PRODUCION_PARTIAL_HOST;

                break;
        }

        return $apiUrl . $this->getRegionTLD();
    }

    protected function getRegionTLD(): string
    {
        switch ($this->getRegion()) {
            case Region::AU:
                return '.com.au';
            case Region::EU:
                return '.eu';
            case Region::IN:
                return '.in';
            case Region::CN:
                return '.com.cn';
            case Region::US:
            default:
                return '.com';
        }
    }

    public function getUrl(): string
    {
        return $this->getBaseUrl() . self::ZOHOCRM_API_URL_PATH;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getRegion(): string
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

    /**
     * @param ResponseInterface $response
     * @return array|string
     * @throws ApiError
     * @throws Exception\AuthFailed
     * @throws Exception\DuplicateData
     * @throws Exception\InvalidData
     * @throws Exception\InvalidDataFormat
     * @throws Exception\InvalidDataType
     * @throws Exception\InvalidModule
     * @throws Exception\InvalidUrlPattern
     * @throws Exception\LimitExceeded
     * @throws Exception\MandatoryDataNotFound
     * @throws Exception\MethodNotAllowed
     * @throws Exception\OAuthScopeMismatch
     * @throws Exception\RequestEntityTooLarge
     * @throws Exception\TooManyRequests
     * @throws Exception\Unauthorized
     * @throws Exception\UnsupportedMediaType
     */
    public function processResult(ResponseInterface $response)
    {
        if (! $this->isSuccessfulResponse($response)) {
            ApiError::throwFromResponse($response);
        }

        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            return $this->getResponseContent($response);
        }

        if (! $result) {
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

        return '';
    }
}
