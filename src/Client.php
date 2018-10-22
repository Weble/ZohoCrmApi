<?php

namespace Webleit\ZohoCrmApi;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Psr\Cache;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Webleit\ZohoCrmApi\Exception\ApiError;
use Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException;
use Webleit\ZohoCrmApi\Exception\NonExistingModule;

/**
 * Class Client
 * @see https://github.com/opsway/zohobooks-api
 * @package Webleit\ZohoBooksApi
 */
class Client
{
    const OAUTH_GRANT_URL_US = "https://accounts.zoho.com/oauth/v2/auth";
    const OAUTH_GRANT_URL_EU = "https://accounts.zoho.eu/oauth/v2/auth";
    const OAUTH_GRANT_URL_CN = "https://accounts.zoho.cn/oauth/v2/auth";

    const OAUTH_API_URL_US = "https://accounts.zoho.com/oauth/v2/token";
    const OAUTH_API_URL_EU = "https://accounts.zoho.eu/oauth/v2/token";
    const OAUTH_API_URL_CN = "https://accounts.zoho.cn/oauth/v2/token";

    const ZOHOCRM_API_URL_PRODUCTION_US = "https://www.zohoapis.com/crm/v2/";
    const ZOHOCRM_API_URL_PRODUCTION_EU = "https://www.zohoapis.eu/crm/v2/";
    const ZOHOCRM_API_URL_PRODUCTION_CN = "https://www.zohoapis.cn/crm/v2/";

    const ZOHOCRM_API_URL_DEVELOPER_US = "https://developer.zoho.com/crm/v2/";
    const ZOHOCRM_API_URL_DEVELOPER_EU = "https://developer.zoho.eu/crm/v2/";
    const ZOHOCRM_API_URL_DEVELOPER_CN = "https://developer.zoho.cn/crm/v2/";

    const ZOHOCRM_API_URL_SANDBOX_US = "https://sandbox.zoho.com/crm/v2/";
    const ZOHOCRM_API_URL_SANDBOX_EU = "https://sandbox.zoho.eu/crm/v2/";
    const ZOHOCRM_API_URL_SANDBOX_CN = "https://sandbox.zoho.cn/crm/v2/";

    const MODE_PRODUCTION = 'production';
    const MODE_DEVELOPER = 'developer';
    const MODE_SANDBOX = 'sandbox';

    const DC_US = 'com';
    const DC_EU = 'eu';
    const DC_CN = 'cn';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $grantCode;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $accessToken = '';

    /**
     * @var string
     */
    protected $refreshToken = '';

    /**
     * @var string
     */
    protected $mode = self::MODE_PRODUCTION;

    /**
     * @var string
     */
    protected $dc = self::DC_US;

    /**
     * @var Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     * Client constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $grantCode
     */
    public function __construct ($clientId, $clientSecret, $refreshToken = null)
    {
        $this->client = new \GuzzleHttp\Client();

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        if ($refreshToken) {
            $this->setRefreshToken($refreshToken);
        }
    }

    /**
     * @return $this
     */
    public function developerMode ()
    {
        $this->mode = self::MODE_DEVELOPER;
        return $this;
    }

    /**
     * @return $this
     */
    public function productionMode ()
    {
        $this->mode = self::MODE_PRODUCTION;
        return $this;
    }

    /**
     * @return $this
     */
    public function sandboxMode ()
    {
        $this->mode = self::MODE_SANDBOX;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode ()
    {
        return $this->mode;
    }

    /**
     * @return $this
     */
    public function euRegion ()
    {
        $this->dc = self::DC_EU;
        return $this;
    }

    /**
     * @return $this
     */
    public function usRegion ()
    {
        $this->dc = self::DC_US;
        return $this;
    }

    /**
     * @return $this
     */
    public function cnRegion ()
    {
        $this->dc = self::DC_CN;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl ()
    {
        switch ($this->mode) {
            case self::MODE_DEVELOPER:
                switch ($this->dc) {
                    case self::DC_CN:
                        return self::ZOHOCRM_API_URL_DEVELOPER_CN;
                        break;
                    case self::DC_EU:
                        return self::ZOHOCRM_API_URL_DEVELOPER_EU;
                        break;
                    case self::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_DEVELOPER_US;
                        break;
                }

                break;
            case self::MODE_SANDBOX:
                switch ($this->dc) {
                    case self::DC_CN:
                        return self::ZOHOCRM_API_URL_SANDBOX_CN;
                        break;
                    case self::DC_EU:
                        return self::ZOHOCRM_API_URL_SANDBOX_EU;
                        break;
                    case self::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_SANDBOX_US;
                        break;
                }

                break;
            case self::MODE_PRODUCTION:
            default:
                switch ($this->dc) {
                    case self::DC_CN:
                        return self::ZOHOCRM_API_URL_PRODUCTION_CN;
                        break;
                    case self::DC_EU:
                        return self::ZOHOCRM_API_URL_PRODUCTION_EU;
                        break;
                    case self::DC_US:
                    default:
                        return self::ZOHOCRM_API_URL_PRODUCTION_US;
                        break;
                }

                break;
        }
    }

    /**
     * @return string
     */
    public function getOAuthApiUrl()
    {
        switch ($this->dc) {
            case self::DC_CN:
                return self::OAUTH_API_URL_CN;
                break;
            case self::DC_EU:
                return self::OAUTH_API_URL_EU;
                break;
            case self::DC_US:
            default:
                return self::OAUTH_API_URL_US;
                break;
        }
    }

    /**
     * @return string
     */
    public function getOAuthGrantUrl()
    {
        switch ($this->dc) {
            case self::DC_CN:
                return self::OAUTH_GRANT_URL_CN;
                break;
            case self::DC_EU:
                return self::OAUTH_GRANT_URL_EU;
                break;
            case self::DC_US:
            default:
                return self::OAUTH_GRANT_URL_US;
                break;
        }
    }

    /**
     * @param string $grantCode
     * @return $this
     */
    public function setGrantCode (string $grantCode)
    {
        $this->grantCode = $grantCode;
        return $this;
    }

    /**
     * @param Cache\CacheItemPoolInterface $cacheItemPool
     * @return $this
     */
    public function useCache (Cache\CacheItemPoolInterface $cacheItemPool)
    {
        $this->cache = $cacheItemPool;
        return $this;
    }

    /**
     * @param $uri
     * @param $method
     * @param $query
     * @param $data
     * @param array $extraData
     * @return Response
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function call ($uri, $method, $query = [], $data = [], $extraData = [])
    {
        $data = [

        ];

        $data = array_merge($data, $extraData);

        try {
            return $this->client->$method($this->getUrl() . $uri, [
                'query' => $query,
                'form_params' => $data,
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $this->getAccessToken()
                ]
            ]);
        } catch (ClientException $e) {
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

            if (in_array($response->code,['INVALID_MODULE', 'INVALID_URL_PATTERN'])) {
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
     * @param array $search
     * @return mixed
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function getList ($uri, $params = [], $start = 1, $limit = 10, $orderBy = 'created_time', $orderDir = 'DESC', $search = [])
    {
        $pageContext = $this->getPageContext($start, $limit, $orderBy, $orderDir, $search);

        $params = array_merge($params,  [
            'data' => json_encode($pageContext)
        ]);

        $response = $this->call($uri, 'GET', $params);

        $body = $response->getBody();

        $data = json_decode($body, true);

        return $data;
    }

    /**
     * @param $url
     * @param null $id
     * @param array $params
     * @return array|mixed|string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function get ($url, $id = null, $params = [])
    {
        if ($id !== null) {
            $url .= '/' . $id;
        }

        return $this->processResult(
            $this->call($url, 'GET', $params)
        );
    }

    /**
     * @param int $start
     * @param int $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param array $search
     * @return array
     */
    protected function getPageContext ($start = 1, $limit = 10, $orderBy = 'created_time', $orderDir = 'DESC', $search = [])
    {
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
     * @param ResponseInterface $response
     * @return array|mixed|string
     * @throws ApiError
     */
    protected function processResult (ResponseInterface $response)
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
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient (): \GuzzleHttp\Client
    {
        return $this->client;
    }

    /**
     * @return mixed
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function getAccessToken ()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        if (!$this->cache) {
            return $this->generateAccessToken();
        }

        try {
            $cachedAccessToken = $this->cache->getItem('zoho_crm_access_token');

            $value = $cachedAccessToken->get();
            if ($value) {
                return $value;
            }

            $accessToken = $this->generateAccessToken();
            $cachedAccessToken->set($accessToken);
            $cachedAccessToken->expiresAfter(60 * 59);
            $this->cache->save($cachedAccessToken);

            return $accessToken;

        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return $this->generateAccessToken();
        }
    }

    /**
     * @return mixed
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    protected function generateAccessToken ()
    {
        $response = $this->client->post($this->getOAuthApiUrl(), [
            'query' => [
                'refresh_token' => $this->getRefreshToken(),
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token'
            ]
        ]);

        $data = json_decode($response->getBody());

        if (!isset($data->access_token)) {
            throw new ApiError(@$data->error);
        }

        $this->setAccessToken($data->access_token, $data->expires_in_sec);

        return $data->access_token;
    }

    /**
     * @return mixed|string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    public function getRefreshToken ()
    {
        if ($this->refreshToken) {
            return $this->refreshToken;
        }

        if (!$this->cache) {
            return $this->generateRefreshToken();
        }

        try {
            $cachedAccessToken = $this->cache->getItem('zoho_crm_refresh_token');

            $value = $cachedAccessToken->get();
            if ($value) {
                return $value;
            }

            $accessToken = $this->generateRefreshToken();
            $cachedAccessToken->set($accessToken);
            $cachedAccessToken->expiresAfter(60 * 59);
            $this->cache->save($cachedAccessToken);

            return $accessToken;

        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return $this->generateRefreshToken();
        }
    }

    /**
     * @param $token
     * @param int $expiresInSeconds
     * @return $this|mixed
     */
    public function setAccessToken ($token, $expiresInSeconds = 3600)
    {
        $this->accessToken = $token;

        if (!$this->cache) {
            return $this;
        }

        try {
            $cachedToken = $this->cache->getItem('zoho_crm_access_token');

            $cachedToken->set($token);
            $cachedToken->expiresAfter($expiresInSeconds);
            $this->cache->save($cachedToken);

            return $this;

        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return $this;
        }
    }

    /**
     * @param $token
     * @param int $expiresInSeconds
     * @return $this|mixed
     */
    public function setRefreshToken ($token, $expiresInSeconds = 3600)
    {
        $this->refreshToken = $token;

        if (!$this->cache) {
            return $this;
        }

        try {
            $cachedToken = $this->cache->getItem('zoho_crm_refresh_token');

            $cachedToken->set($token);
            $cachedToken->expiresAfter($expiresInSeconds);
            $this->cache->save($cachedToken);

            return $this;

        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return $this;
        }
    }


    /**
     * @return string
     * @throws ApiError
     * @throws GrantCodeNotSetException
     */
    protected function generateRefreshToken ()
    {
        if (!$this->grantCode) {
            throw new GrantCodeNotSetException('You need to pass a grant code to use the Api. To generate a grant code visit ' . $this->getGrantCodeConsentUrl());
        }

        $response = $this->client->post($this->getOAuthApiUrl(), [
            'query' => [
                'code' => $this->grantCode,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'state' => 'testing',
                'grant_type' => 'authorization_code',
                'scope' => 'ZohoCRM.users.all,ZohoCRM.settings.all,ZohoCRM.modules.all,ZohoCRM.org.all'
            ]
        ]);

        $data = json_decode($response->getBody());

        if (!isset($data->refresh_token)) {
            throw new ApiError(@$data->error);
        }

        $this->setAccessToken($data->access_token, $data->expires_in_sec);
        $this->setRefreshToken($data->refresh_token, $data->expires_in_sec);

        return $data->refresh_token;
    }

    /**
     * @param $redirectUri
     * @return string
     */
    public function getGrantCodeConsentUrl ($redirectUri)
    {
        return $this->getOAuthGrantUrl() . '?' . http_build_query([
                'client_id' => $this->clientId,
                'state' => 'testing',
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'access_type' => 'offline',
                'scope' => 'ZohoCRM.users.all,ZohoCRM.settings.all,ZohoCRM.modules.all,ZohoCRM.org.all'
            ]);
    }

    /**
     * @param UriInterface $uri
     * @return string|null
     */
    public static function parseGrantTokenFromUrl (UriInterface $uri)
    {
        $query = $uri->getQuery();
        $data = explode('&', $query);

        foreach ($data as &$d) {
            $d = explode("=", $d);
        }

        if (isset($data['code'])) {
            return $data['code'];
        }

        return null;
    }
}