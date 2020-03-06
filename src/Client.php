<?php

namespace Webleit\ZohoCrmApi;

use BenTools\GuzzleHttp\Middleware\Storage\Adapter\ArrayAdapter;
use BenTools\GuzzleHttp\Middleware\ThrottleConfiguration;
use BenTools\GuzzleHttp\Middleware\ThrottleMiddleware;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Weble\ZohoClient\Enums\Region;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoCrmApi\Enums\Mode;
use Webleit\ZohoCrmApi\Exception\ApiError;
use Webleit\ZohoCrmApi\Exception\NonExistingModule;
use Webleit\ZohoCrmApi\Request\RequestMatcher;

class Client
{
    const ZOHOCRM_API_URL_PATH = "/crm/v2/";
    const ZOHOCRM_API_PRODUCION_PARTIAL_HOST = "https://www.zohoapis";
    const ZOHOCRM_API_DEVELOPER_PARTIAL_HOST = "https://developer.zoho";
    const ZOHOCRM_API_SANDBOX_PARTIAL_HOST = "https://crmsandbox.zoho";

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
            $name
        ], $arguments);
    }

    public function throttle(int $maxRequests = 0, float $duration = 0): self
    {
        $this->throttle = ($maxRequests > 0) ? true : false;

        if ($this->throttle) {
            $this->enableThrottling($maxRequests, $duration);
        } else {
            $this->client = new \GuzzleHttp\Client();
        }

        return $this;
    }

    protected function enableThrottling(int $maxRequests, float $duration): self
    {
        $stack = HandlerStack::create();
        $middleware = new ThrottleMiddleware(new ArrayAdapter());

        $middleware->registerConfiguration(new ThrottleConfiguration(new RequestMatcher(), $maxRequests, $duration, 'zoho'));

        $stack->push($middleware, 'throttle');
        $this->client = new \GuzzleHttp\Client(['handler' => $stack]);

        return $this;
    }

    /**
     * @deprecated
     */
    public function developerMode(): self
    {
        return $this->setMode(Mode::developer());
    }

    /**
     * @deprecated
     */
    public function productionMode(): self
    {
        return $this->setMode(Mode::production());
    }

    /**
     * @deprecated
     */
    public function sandboxMode(): self
    {
        return $this->setMode(Mode::sandbox());
    }

    /**
     * @deprecated
     */
    public function euRegion(): self
    {
        return $this->setRegion(Region::eu());
    }

    public function setRegion(Region $region): self
    {
        $this->oAuthClient->setRegion($region);
        return $this;
    }

    /**
     * @deprecated
     */
    public function inRegion(): self
    {
        return $this->setRegion(Region::in());
    }

    /**
     * @deprecated
     */
    public function usRegion(): self
    {
        return $this->setRegion(Region::us());
    }

    /**
     * @deprecated
     */
    public function cnRegion(): self
    {
        return $this->setRegion(Region::cn());
    }

    public function getList(string $uri, array $params = [], int $start = 1, int $limit = 10, string $orderBy = 'created_time', string $orderDir = 'DESC', array $search = []): array
    {
        $pageContext = $this->getPageContext($start, $limit, $orderBy, $orderDir, $search);

        $params = array_merge($params, ['data' => json_encode($pageContext)]);

        $response = $this->call($uri, 'GET', ['query' => $params]);

        $body = $response->getBody();

        $data = json_decode($body, true);

        return $data;
    }

    protected function getPageContext(int $start = 1, int $limit = 10, string $orderBy = 'created_time', string $orderDir = 'DESC', array $search = [])
    {
        return [
            'page_context' => [
                'row_count'   => $limit,
                'start_index' => $start,
                //'search_columns' => $search,
                'sort_column' => $orderBy,
                'sort_order'  => $orderDir
            ]
        ];
    }

    /**
     * @throws NonExistingModule
     * @throws \Weble\ZohoClient\Exception\AccessDeniedException
     * @throws \Weble\ZohoClient\Exception\ApiError
     * @throws \Weble\ZohoClient\Exception\CannotGenerateAccessToken
     * @throws \Weble\ZohoClient\Exception\CannotGenerateRefreshToken
     * @throws \Weble\ZohoClient\Exception\GrantCodeNotSetException
     * @throws \Weble\ZohoClient\Exception\InvalidGrantCodeException
     * @throws \Weble\ZohoClient\Exception\RefreshTokenNotSet
     */
    public function call(string $uri, string $method, array $data = [])
    {
        $options = array_merge([
            'query'       => [],
            'form_params' => [],
            'json'        => [],
            'headers'     => ['Authorization' => 'Zoho-oauthtoken ' . $this->oAuthClient->getAccessToken()]
        ], $data);

        try {
            return $this->client->$method($this->getUrl() . $uri, $options);
        } catch (ClientException $e) {

            // Retry?
            if ($e->getCode() === 401) {
                $this->oAuthClient->generateTokens()->getAccessToken();
                return $this->call($uri, $method, $data);
            }

            $response = $e->getResponse();

            if (!$response) {
                throw $e;
            }

            $response = json_decode($response->getBody());
            if (!$response) {
                throw $e;
            }

            if (!isset($response->code)) {
                throw $e;
            }

            if (in_array($response->code, [
                'INVALID_MODULE',
                'INVALID_URL_PATTERN'
            ])) {
                throw new NonExistingModule($response->message);
            }

            throw $e;
        }
    }

    public function getUrl(): string
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

        $apiUrl .= $this->getRegion()->getValue();
        $apiUrl .= self::ZOHOCRM_API_URL_PATH;

        return $apiUrl;
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

    /**
     * @throws ApiError
     * @throws NonExistingModule
     * @throws \Weble\ZohoClient\Exception\AccessDeniedException
     * @throws \Weble\ZohoClient\Exception\ApiError
     * @throws \Weble\ZohoClient\Exception\CannotGenerateAccessToken
     * @throws \Weble\ZohoClient\Exception\CannotGenerateRefreshToken
     * @throws \Weble\ZohoClient\Exception\GrantCodeNotSetException
     * @throws \Weble\ZohoClient\Exception\InvalidGrantCodeException
     * @throws \Weble\ZohoClient\Exception\RefreshTokenNotSet
     */
    public function get(string $url, string $id = null, array $params = [])
    {
        if ($id !== null) {
            $url .= '/' . $id;
        }

        return $this->processResult($this->call($url, 'GET', ['query' => $params]));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array|mixed|string
     * @throws ApiError
     */
    protected function processResult(ResponseInterface $response)
    {
        // All ok, probably not json, like PDF?
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new ApiError('Response from Zoho is not success. Message: ' . $response->getReasonPhrase());
        }

        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {

            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string)$response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: ' . $response->getReasonPhrase());
        }

        if (!$result) {
            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string)$response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: ' . $response->getReasonPhrase());
        }

        return $result;
    }

    /**
     * @return array|mixed|string
     * @throws ApiError
     * @throws NonExistingModule
     * @throws \Weble\ZohoClient\Exception\AccessDeniedException
     * @throws \Weble\ZohoClient\Exception\ApiError
     * @throws \Weble\ZohoClient\Exception\CannotGenerateAccessToken
     * @throws \Weble\ZohoClient\Exception\CannotGenerateRefreshToken
     * @throws \Weble\ZohoClient\Exception\GrantCodeNotSetException
     * @throws \Weble\ZohoClient\Exception\InvalidGrantCodeException
     * @throws \Weble\ZohoClient\Exception\RefreshTokenNotSet
     */
    public function post(string $url, array $params = [], array $queryParams = [])
    {
        return $this->processResult($this->call($url, 'POST', [
            'query' => $queryParams,
            'json'  => $params
        ]));
    }

    /**
     * @throws ApiError
     * @throws NonExistingModule
     * @throws \Weble\ZohoClient\Exception\AccessDeniedException
     * @throws \Weble\ZohoClient\Exception\ApiError
     * @throws \Weble\ZohoClient\Exception\CannotGenerateAccessToken
     * @throws \Weble\ZohoClient\Exception\CannotGenerateRefreshToken
     * @throws \Weble\ZohoClient\Exception\GrantCodeNotSetException
     * @throws \Weble\ZohoClient\Exception\InvalidGrantCodeException
     * @throws \Weble\ZohoClient\Exception\RefreshTokenNotSet
     */
    public function put(string $url, array $params = [], array $queryParams = [])
    {
        return $this->processResult($this->call($url, 'PUT', [
            'query' => $queryParams,
            'json'  => $params
        ]));
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }
}
