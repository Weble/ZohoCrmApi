<?php

namespace Webleit\ZohoCrmApi\Test;

use PHPUnit\Framework\TestCase;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Models\Request;
use Webleit\ZohoCrmApi\Models\Settings\Layout;
use Webleit\ZohoCrmApi\Models\Settings\Module;
use Webleit\ZohoCrmApi\Models\Template;
use Webleit\ZohoCrmApi\Models\User;
use Webleit\ZohoCrmApi\ZohoCrm;

/**
 * Class ClassNameGeneratorTest
 * @package Webleit\ZohoBooksApi\Test
 */
class ApiTest extends TestCase
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var ZohoCrm
     */
    protected static $zoho;

    /**
     * setup
     */
    public static function setUpBeforeClass()
    {

        $authFile = __DIR__ . '/config.example.json';
        if (file_exists(__DIR__ . '/config.json')) {
            $authFile = __DIR__ . '/config.json';
        }

        $auth = json_decode(file_get_contents($authFile));

        $client = new ZohoCrm($auth->client_id, $auth->client_secret);
        $client->setRefreshToken($auth->refresh_token);

        self::$client = $client->getClient()->usRegion()->developerMode();
        self::$zoho = $client;
    }

    /**
     * test
     */
    public function hasAccessToken()
    {
        $accessToken = self::$client->getAccessToken();
        $this->assertTrue(strlen($accessToken) > 0);
    }

    /**
     * test
     */
    public function canCanListOfModules()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->getList()->count());
    }

    /**
     * test
     */
    public function canGetModuleDetails()
    {
        $this->assertEquals('Leads', self::$zoho->settings->modules->get('Leads')->module_name);
    }

    /**
     * test
     */
    public function canGetModuleFields()
    {
        $this->assertGreaterThan(0,  self::$zoho->settings->modules->get('Leads')->getFields()->count());
    }

    /**
     * test
     */
    public function canGetModuleLayouts()
    {
        $this->assertGreaterThan(0,  self::$zoho->settings->modules->get('Leads')->getLayouts()->count());
    }

    /**
     * test
     */
    public function canGetModuleRelatedLists()
    {
        $this->assertGreaterThan(0,  self::$zoho->settings->modules->get('Leads')->getRelatedLists()->count());
    }

    /**
     * test
     */
    public function canGetModuleCustomViews()
    {
        $this->assertGreaterThan(0,  self::$zoho->settings->modules->get('Leads')->getCustomViews()->count());
    }

    /**
     * @test
     */
    public function canGetSingleCustomView()
    {
        $layouts = self::$zoho->settings->modules->get('Leads')->getCustomViews();
        $leads =  self::$zoho->settings->modules->get('Leads');
        /** @var Layout $user */
        $layout = $layouts->first();
        $this->assertEquals($layout->getId(), $leads->getCustomView($layout->getId())->getId());
    }

    /**
     * test
     */
    public function canGetSingleLayout()
    {
        $layouts = self::$zoho->settings->modules->get('Leads')->getLayouts();
        $leads =  self::$zoho->settings->modules->get('Leads');
        /** @var Layout $user */
        $layout = $layouts->first();
        $this->assertEquals($layout->getId(), $leads->getLayout($layout->getId())->getId());
    }

    /**
     * test
     */
    public function canGetListOfUsers()
    {
        $this->assertGreaterThan(0, self::$zoho->users->getList()->count());
    }

    /**
     * test
     */
    public function canGetSingleUser()
    {
        $users =  self::$zoho->users->getList();

        /** @var User $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->users->get($user->getId())->getId());
    }

    /**
     * test
     */
    public function canListRoles()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->roles->getList()->count());
    }

    /**
     * test
     */
    public function canGetSingleRole()
    {
        $users =  self::$zoho->settings->roles->getList();

        /** @var Role $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->settings->roles->get($user->getId())->getId());
    }

    /**
     * test
     */
    public function canListProfiles()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->profiles->getList()->count());
    }

    /**
     * test
     */
    public function canGetSingleProfile()
    {
        $users =  self::$zoho->settings->profiles->getList();

        /** @var Role $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->settings->profiles->get($user->getId())->getId());
    }

    /**
     * test
     */
    public function getCorrectApiUrl()
    {
        self::$client->sandboxMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_SANDBOX_US, self::$client->getUrl());
        self::$client->developerMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_DEVELOPER_US, self::$client->getUrl());
        self::$client->productionMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_PRODUCTION_US, self::$client->getUrl());

        self::$client->cnRegion()->sandboxMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_SANDBOX_CN, self::$client->getUrl());
        self::$client->cnRegion()->developerMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_DEVELOPER_CN, self::$client->getUrl());
        self::$client->cnRegion()->productionMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_PRODUCTION_CN, self::$client->getUrl());

        self::$client->euRegion()->sandboxMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_SANDBOX_EU, self::$client->getUrl());
        self::$client->euRegion()->developerMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_DEVELOPER_EU, self::$client->getUrl());
        self::$client->euRegion()->productionMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_PRODUCTION_EU, self::$client->getUrl());

        self::$client->usRegion()->sandboxMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_SANDBOX_US, self::$client->getUrl());
        self::$client->usRegion()->developerMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_DEVELOPER_US, self::$client->getUrl());
        self::$client->usRegion()->productionMode();
        $this->assertEquals(Client::ZOHOCRM_API_URL_PRODUCTION_US, self::$client->getUrl());

        self::$client->usRegion()->developerMode();
    }
}