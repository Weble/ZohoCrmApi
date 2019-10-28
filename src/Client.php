<?php

namespace Webleit\ZohoCrmApi;

use BenTools\GuzzleHttp\Middleware\Storage\Adapter\ArrayAdapter;
use BenTools\GuzzleHttp\Middleware\ThrottleConfiguration;
use BenTools\GuzzleHttp\Middleware\ThrottleMiddleware;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoCrmApi\Exception\ApiError;
use Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException;
use Webleit\ZohoCrmApi\Exception\NonExistingModule;
use Webleit\ZohoCrmApi\Request\RequestMatcher;

/**
 * Class Client
 * @see https://github.com/opsway/zohobooks-api
 * @package Webleit\ZohoBooksApi
 */
class Client
{
    const ZOHOCRM_API_URL_PRODUCTION_US = "https://www.zohoapis.com/crm/v2/";
    const ZOHOCRM_API_URL_PRODUCTION_EU = "https://www.zohoapis.eu/crm/v2/";
    const ZOHOCRM_API_URL_PRODUCTION_CN = "https://www.zohoapis.cn/crm/v2/";

    const ZOHOCRM_API_URL_DEVELOPER_US = "https://developer.zoho.com/crm/v2/";
    const ZOHOCRM_API_URL_DEVELOPER_EU = "https://developer.zoho.eu/crm/v2/";
    const ZOHOCRM_API_URL_DEVELOPER_CN = "https://developer.zoho.cn/crm/v2/";

    const ZOHOCRM_API_URL_SANDBOX_US = "https://crmsandbox.zoho.com/crm/v2/";
    const ZOHOCRM_API_URL_SANDBOX_EU = "https://crmsandbox.zoho.eu/crm/v2/";
    const ZOHOCRM_API_URL_SANDBOX_CN = "https://crmsandbox.zoho.cn/crm/v2/";

    const MODE_PRODUCTION = 'production';
    const MODE_DEVELOPER = 'developer';
    const MODE_SANDBOX = 'sandbox';

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
     * @var string
     */
    protected $mode = self::MODE_PRODUCTION;

    /**
     * @var string
     */
    protected $dc = OAuthClient::DC_US;

    /**
     * Client constructor.
     *
     * @param $clientId
     * @param $clientSecret
     * @param $refreshToken
     */
    public function __construct($clientId, $clientSecret, $refreshToken = null)
    {
        $this->client = new \GuzzleHttp\Client();
        $this->oAuthClient = new OAuthClient($clientId, $clientSecret, $refreshToken);
        $this->oAuthClient->setRefreshToken($refreshToken);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->oAuthClient, $name], $arguments);
    }

    /**
     * @param  bool  $maxRequests
     * @param  bool  $duration
     *
     * @return $this
     */
    public function throttle($maxRequests = false, $duration = false)
    {
        $this->throttle = $maxRequests ? true : false;

        if ($this->throttle) {
            $this->enableThrottling($maxRequests, $duration);
        } else {
            $this->client = new \GuzzleHttp\Client();
        }

        return $this;
    }

    /**
     * @param $maxRequests
     * @param $duration
     */
    protected function enableThrottling($maxRequests, $duration)
    {
        $stack = HandlerStack::create();
        $middleware = new ThrottleMiddleware(new ArrayAdapter());

        $middleware->registerConfiguration(
            new ThrottleConfiguration(new RequestMatcher(), $maxRequests, $duration, 'zoho')
        );

        $stack->push($middleware, 'throttle');
        $this->client = new \GuzzleHttp\Client([
            'handler' => $stack
        ]);
    }

    /**
     * @return $this
     */
    public function developerMode()
    {
        $this->mode = self::MODE_DEVELOPER;
        return $this;
    }

    /**
     * @return $this
     */
    public function productionMode()
    {
        $this->mode = self::MODE_PRODUCTION;
        return $this;
    }

    /**
     * @return $this
     */
    public function sandboxMode()
    {
        $this->mode = self::MODE_SANDBOX;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return $this
     */
    public function euRegion()
    {
        $this->dc = OAuthClient::DC_EU;
        $this->oAuthClient->euRegion();
        return $this;
    }

    public function inRegion()
    {
        $this->dc = OAuthClient::DC_IN;
        $this->oAuthClient->inRegion();
        return $this;
    }

    /**
     * @return $this
     */
    public function usRegion()
    {
        $this->dc = OAuthClient::DC_US;
        $this->oAuthClient->usRegion();
        return $this;
    }

    /**
     * @return $this
     */
    public function cnRegion()
    {
        $this->dc = OAuthClient::DC_CN;
        $this->oAuthClient->cnRegion();
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        switch ($this->mode) {
            case self::MODE_DEVELOPER:
                switch ($this->dc) {
                    case OAuthClient::DC_CN:
                        return self::ZOHOCRM_API_URL_DEVELOPER_CN;
                        break;
                    case OAuthClient::DC_EU:
                        return self::ZOHOCRM_API_URL_DEVELOPER_EU;
                        break;
                    case OAuthClient::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_DEVELOPER_US;
                        break;
                }

                break;
            case self::MODE_SANDBOX:
                switch ($this->dc) {
                    case OAuthClient::DC_CN:
                        return self::ZOHOCRM_API_URL_SANDBOX_CN;
                        break;
                    case OAuthClient::DC_EU:
                        return self::ZOHOCRM_API_URL_SANDBOX_EU;
                        break;
                    case OAuthClient::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_SANDBOX_US;
                        break;
                }

                break;
            case self::MODE_PRODUCTION:
            default:
                switch ($this->dc) {
                    case OAuthClient::DC_CN:
                        return self::ZOHOCRM_API_URL_PRODUCTION_CN;
                        break;
                    case OAuthClient::DC_EU:
                        return self::ZOHOCRM_API_URL_PRODUCTION_EU;
                        break;
                    case OAuthClient::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_PRODUCTION_US;
                        break;
                }

                break;
        }
    }

    /**
     * @param $uri
     * @param $method
     * @param  array  $data
     *
     * @return mixed
     * @throws ApiError
     * @throws GrantCodeNotSetException
     * @throws NonExistingModule
     */
    public function call($uri, $method, $data = [])
    {
        $options = array_merge([
            'query' => [],
            'form_params' => [],
            'json' => [],
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken '.$this->oAuthClient->getAccessToken()
            ]
        ], $data);

        try {
            return $this->client->$method($this->getUrl().$uri, $options);
        } catch (ClientException $e) {

            // Retry?
            if ($e->getCode() === 401) {
                $this->oAuthClient->generateAccessToken();
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

            if (in_array($response->code, ['INVALID_MODULE', 'INVALID_URL_PATTERN'])) {
                throw new NonExistingModule($response->message);
            }

            throw $e;
        }
    }

    /**
     * @param $uri
     * @param $params
     * @param $start
     * @param $limit
     * @param $orderBy
     * @param $orderDir
     * @param  array  $search
     *
     * @return mixed
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function getList(
        $uri,
        $params = [],
        $start = 1,
        $limit = 10,
        $orderBy = 'created_time',
        $orderDir = 'DESC',
        $search = []
    ) {
        $pageContext = $this->getPageContext($start, $limit, $orderBy, $orderDir, $search);

        $params = array_merge($params, [
            'data' => json_encode($pageContext)
        ]);

        $response = $this->call($uri, 'GET', ['query' => $params]);

        $body = $response->getBody();

        $data = json_decode($body, true);

        return $data;
    }

    /**
     * @param $url
     * @param  null  $id
     * @param  array  $params
     *
     * @return array|mixed|string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function get($url, $id = null, $params = [])
    {
        if ($id !== null) {
            $url .= '/'.$id;
        }

        return $this->processResult(
            $this->call($url, 'GET', ['query' => $params])
        );
    }

    /**
     * @param $url
     * @param  array  $params
     * @param  array  $queryParams
     *
     * @return array|mixed|string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     * @throws NonExistingModule
     */
    public function post($url, $params = [], $queryParams = [])
    {
        return $this->processResult(
            $this->call($url, 'POST', [
                'query' => $queryParams,
                'json' => $params
            ])
        );
    }

    /**
     * @param $url
     * @param  array  $params
     * @param  array  $queryParams
     *
     * @return array|mixed|string
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function put($url, $params = [], $queryParams = [])
    {
        return $this->processResult(
            $this->call($url, 'PUT', [
                'query' => $queryParams,
                'json' => $params
            ])
        );
    }

    /**
     * @param  int  $start
     * @param  int  $limit
     * @param  string  $orderBy
     * @param  string  $orderDir
     * @param  array  $search
     *
     * @return array
     */
    protected function getPageContext(
        $start = 1,
        $limit = 10,
        $orderBy = 'created_time',
        $orderDir = 'DESC',
        $search = []
    ) {
        return [
            'page_context' => [
                'row_count' => $limit,
                'start_index' => $start,
                //'search_columns' => $search,
                'sort_column' => $orderBy,
                'sort_order' => $orderDir
            ]
        ];
    }

    /**
     * @param  ResponseInterface  $response
     *
     * @return array|mixed|string
     * @throws ApiError
     */
    protected function processResult(ResponseInterface $response)
    {
        // All ok, probably not json, like PDF?
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        try {
            $result = json_decode($response->getBody(), true);
        } catch (\InvalidArgumentException $e) {

            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        if (!$result) {
            // All ok, probably not json, like PDF?
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
                return (string) $response->getBody();
            }

            throw new ApiError('Response from Zoho is not success. Message: '.$response->getReasonPhrase());
        }

        return $result;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }

    /**
     * @param  CacheItemPoolInterface  $cacheItemPool
     *
     * @return $this
     */
    public function useCache(CacheItemPoolInterface $cacheItemPool)
    {
        $this->oAuthClient->useCache($cacheItemPool);
        return $this;
    }
}