<?php

namespace Webleit\ZohoCrmApi;

use Closure;
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
    protected const ZOHOCRM_API_URL_PATH = "/crm/v4/";
    protected const ZOHOCRM_API_PRODUCION_PARTIAL_HOST = "https://www.zohoapis";
    protected const ZOHOCRM_API_DEVELOPER_PARTIAL_HOST = "https://developer.zoho";
    protected const ZOHOCRM_API_SANDBOX_PARTIAL_HOST = "https://crmsandbox.zoho";

    public const SUCCESS_CODE = 'SUCCESS';

    protected bool $retriedRefresh = false;
    protected ClientInterface $client;
    protected OAuthClient $oAuthClient;

    protected string $mode;

    public function __construct(OAuthClient $oAuthClient, ?ClientInterface $client = null, string $mode = Mode::PRODUCTION)
    {
        if (! $client) {
            $client = new \GuzzleHttp\Client();
        }

        $this->client = $client;
        $this->oAuthClient = $oAuthClient;

        $this->setMode($mode);
    }

    /**
     * @param string $name
     * @param array<string|int,mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        /** @var Closure $callback */
        $callback = [
            $this->oAuthClient,
            $name,
        ];
        return call_user_func_array($callback, $arguments);
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
     * @param array<string,mixed>|ListParameters $params
     * @param array<string,mixed>|ListHeaders $headers
     * @return array<string|int,mixed>
     */
    public function getList(string $uri, array|ListParameters $params = [], array|ListHeaders $headers = []): array
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

    /**
     * @param array<string|int,mixed> $data
     */
    public function call(string $uri, string $method, array $data = []): ResponseInterface
    {
        $options = array_merge([
            'query' => [],
            'form_params' => [],
            'json' => [],
        ], $data);

        $options['headers'] = array_merge($data['headers'] ?? [], [
            'Authorization' => 'Zoho-oauthtoken ' . $this->oAuthClient->getAccessToken(),
        ]);

        try {
            $response = $this->client->$method($this->getUrl() . $uri, array_filter($options));

            ApiError::throwFromResponse($response);

            return $response;
        } catch (ClientException $e) {
            // Retry?
            if ($e->getCode() === 401 && ! $this->retriedRefresh) {
                $this->oAuthClient->refreshAccessToken();
                $this->retriedRefresh = true;

                return $this->call($uri, $method, $data);
            }

            $response = $e->getResponse();

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

    /**
     * @param array<string|int,mixed> $params
     * @param array<string|int,mixed> $options
     * @return array<string|int,mixed>|string
     */
    public function get(string $url, string $id = null, array $params = [], array $options = []): array|string
    {
        if ($id !== null) {
            $url .= '/' . $id;
        }

        $options['query'] = array_merge($options['query'] ?? [], $params);
        $result = $this->call($url, 'GET', $options);

        return $this->processResult($result);
    }

    /**
     * @param array<string|int,mixed> $params
     * @param array<string|int,mixed> $queryParams
     * @return array<string|int,mixed>|string
     */
    public function post(string $url, array $params = [], array $queryParams = []): array|string
    {
        return $this->processResult($this->call($url, 'POST', [
            'query' => $queryParams,
            'json' => $params,
        ]));
    }

    /**
     * @param array<string|int,mixed> $params
     * @param array<string|int,mixed> $queryParams
     * @return array<string|int,mixed>|string
     */
    public function put(string $url, array $params = [], array $queryParams = []): array|string
    {
        return $this->processResult($this->call($url, 'PUT', [
            'query' => $queryParams,
            'json' => $params,
        ]));
    }

    /**
     * @return array<string|int,mixed>|string
     */
    public function delete(string $url, string $id): array|string
    {
        return $this->processResult($this->call($url . '/' . $id, 'DELETE'));
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param ResponseInterface $response
     * @return array<int|string,mixed>|string
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
