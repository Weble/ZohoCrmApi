<?php

namespace Webleit\ZohoCrmApi\Test;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Weble\ZohoClient\Enums\Region;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Enums\Mode;
use Webleit\ZohoCrmApi\Enums\UserType;
use Webleit\ZohoCrmApi\Models\Settings\Layout;
use Webleit\ZohoCrmApi\Models\User;
use Webleit\ZohoCrmApi\Modules\Records;
use Webleit\ZohoCrmApi\ZohoCrm;

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
    public static function setUpBeforeClass():void
    {
        $auth = self::getConfig();

        $oAuthClient = self::createOAuthClient();
        $client = new Client($oAuthClient);
        $client->throttle(1, 1);
        $client->setMode(Mode::make($auth->mode ?? 'sandbox'));

        self::$client = $client;
        self::$zoho = new ZohoCrm($client);
    }

    protected static function createOAuthClient(): OAuthClient
    {
        $auth = self::getConfig();

        $region = Region::us();
        if ($auth->region) {
            $region = Region::make($auth->region);
        }

        $filesystemAdapter = new Local(sys_get_temp_dir());
        $filesystem = new Filesystem($filesystemAdapter);
        $pool = new FilesystemCachePool($filesystem);

        $client = new OAuthClient($auth->client_id, $auth->client_secret);
        $client->setRefreshToken($auth->refresh_token);
        $client->setRegion($region);
        $client->offlineMode();
        $client->useCache($pool);

        return $client;
    }

    private static function getConfig(): \stdClass
    {
        $authFile = __DIR__ . '/config.example.json';
        if (file_exists(__DIR__ . '/config.json')) {
            // $authFile = __DIR__ . '/config.json';
        }

        $config = json_decode(file_get_contents($authFile));

        foreach ($config as $key => $value) {
            $envValue = $_SERVER[strtoupper('ZOHO_' . $key)] ?? null;
            if ($envValue) {
                $config->$key = $envValue;
            }
        }

        dd($config);

        return $config;
    }

    /**
     * @test
     */
    public function canGetListOfModules()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->getList()->count());
    }

    /**
     * @test
     */
    public function canGetModuleDetails()
    {
        $this->assertEquals('Leads', self::$zoho->settings->modules->get('Leads')->module_name);
    }

    /**
     * @test
     */
    public function canGetModuleFields()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->get('Leads')->getFields()->count());
    }

    /**
     * @test
     */
    public function canGetModuleLayouts()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->get('Leads')->getLayouts()->count());
    }

    /**
     * @test
     */
    public function canGetModuleRelatedLists()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->get('Leads')->getRelatedLists()->count());
    }

    /**
     * @test
     */
    public function canGetModuleCustomViews()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->modules->get('Leads')->getCustomViews()->count());
    }

    /**
     * @test
     */
    public function canGetSingleCustomView()
    {
        $layouts = self::$zoho->settings->modules->get('Leads')->getCustomViews();
        $leads = self::$zoho->settings->modules->get('Leads');
        /** @var Layout $user */
        $layout = $layouts->first();
        $this->assertEquals($layout->getId(), $leads->getCustomView($layout->getId())->getId());
    }

    /**
     * @test
     */
    public function canGetSingleLayout()
    {
        $layouts = self::$zoho->settings->modules->get('Leads')->getLayouts();
        $leads = self::$zoho->settings->modules->get('Leads');
        /** @var Layout $user */
        $layout = $layouts->first();
        $this->assertEquals($layout->getId(), $leads->getLayout($layout->getId())->getId());
    }

    /**
     * @test
     */
    public function canGetListOfUsers()
    {
        $this->assertGreaterThan(0, self::$zoho->users->getList()->count());
    }

    /**
     * @test
     */
    public function canGetListOfCoreModuleRecords()
    {
        $modules = self::$zoho->getApiModules();

        // Unreachable modules

        $this->assertGreaterThan(0, $modules->count());
    }

    /**
     * @test
     */
    public function canCreateLead()
    {
        /** @var Records $leadModule */
        $leadModule = self::$zoho->leads;
        $response = $leadModule->create([
            'Last_Name' => 'Doe',
            'First_Name' => 'John',
        ]);

        $this->assertNotEmpty($response->getId());

        $lead = self::$zoho->leads->get($response->getId());


        $this->assertEquals('John', $lead->First_Name);
        $this->assertEquals('Doe', $lead->Last_Name);
    }

    /**
     * @test
     */
    public function canUpdateLead()
    {
        /** @var Records $leadModule */
        $leadModule = self::$zoho->leads;
        /** @var \Webleit\ZohoCrmApi\Models\Record $lead */
        $lead = self::$zoho->leads->getList()->first();

        $response = $leadModule->update($lead->getId(), [
            'Last_Name' => 'NoName',
        ]);

        $this->assertNotEmpty($response->getId());

        $lead = self::$zoho->leads->get($response->getId());

        $this->assertEquals('NoName', $lead->Last_Name);
    }

    /**
     * @test
     */
    public function canCreateLeads()
    {
        $data = [
            [
                'Last_Name' => 'Doe',
                'First_Name' => 'John',
            ],
            [
                'Last_Name' => 'Doe',
                'First_Name' => 'John',
            ],
            [
                'Last_Name' => 'Doe',
                'First_Name' => 'John',
            ],
        ];

        /** @var Records $leadModule */
        $leadModule = self::$zoho->leads;
        $responses = $leadModule->createMany($data);

        $responses->each(function ($response) {
            $this->assertNotEmpty($response->getId());
        });

        foreach ($data as $k => $row) {
            $response = $responses->get($k);
            $lead = self::$zoho->leads->get($response->getId());
            $this->assertEquals($row['Last_Name'], $lead->Last_Name);
        }
    }

    /**
     * @test
     */
    public function canCreateLeadsEvenWhenErrorOccurs()
    {
        $data = [
            [
                'Last_Name' => 'Doe',
                'First_Name' => 'John',
            ],
            [
                'First_Name' => 'John',
                // This one misses last name, and will fail
            ],
            [
                'Last_Name' => 'Doe',
                'First_Name' => 'John',
            ],
        ];

        /** @var Records $leadModule */
        $leadModule = self::$zoho->leads;
        $responses = $leadModule->createMany($data);

        $this->assertNotEmpty($responses[0]->getId());
        $this->assertEmpty($responses[1]);
        $this->assertNotEmpty($responses[2]->getId());
    }


    /**
     * @test
     */
    public function canConvertLead()
    {
        $leadModule = self::$zoho->leads;
        $response = $leadModule->create([
            'Last_Name' => 'Doe',
            'First_Name' => 'John',
        ]);

        $conversions = $leadModule->convertLead($response->getId());

        $this->assertArrayHasKey('Contacts', $conversions);
        $this->assertArrayHasKey('Deals', $conversions);
    }

    /**
     * @test
     */
    public function canGetSingleUser()
    {
        $users = self::$zoho->users->getList();

        /** @var User $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->users->get($user->getId())->getId());
    }

    /**
     * @test
     */
    public function canGetCurrentUser()
    {
        $user = self::$zoho->users->current();
        $this->assertNotNull($user);

        $users = self::$zoho->users->ofType(UserType::current());
        $this->assertTrue($users->contains($user));
    }

    /**
     * @test
     */
    public function canListRoles()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->roles->getList()->count());
    }

    /**
     * @test
     */
    public function canGetSingleRole()
    {
        $users = self::$zoho->settings->roles->getList();

        /** @var Role $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->settings->roles->get($user->getId())->getId());
    }

    /**
     * @test
     */
    public function canListProfiles()
    {
        $this->assertGreaterThan(0, self::$zoho->settings->profiles->getList()->count());
    }

    /**
     * @test
     */
    public function canGetSingleProfile()
    {
        $users = self::$zoho->settings->profiles->getList();

        /** @var Role $user */
        $user = $users->first();
        $this->assertEquals($user->getId(), self::$zoho->settings->profiles->get($user->getId())->getId());
    }

    /**
     * @test
     */
    public function canCreateSalesOrder()
    {
        /** @var Records $module */
        $module = new Records(self::$client, 'Sales_Orders');

        /** @var Records $module */
        $productsModule = new Records(self::$client, 'Products');
        $product = $productsModule->create([
            'Product_Name' => 'Test',
        ]);

        $response = $module->create([
            'Subject' => '123',
            'Product_Details' => [
                [
                    'product' => $product->getId(),
                    'Unit_Price' => 10,
                    'quantity' => 2,
                ],
            ],
        ]);

        $this->assertNotEmpty($response->getId());

        $order = $module->get($response->getId());

        $this->assertEquals('123', $order->Subject);
    }

    /**
     * @test
     */
    public function canGetCustomModuleRecord()
    {
        /** @var Records $module */
        $module = self::$zoho->tests;

        $name = uniqid();
        $response = $module->create([
            'Name' => $name,
        ]);

        $this->assertNotEmpty($response->getId());
        $item = $module->get($response->getId());
        $this->assertEquals($name, $item->Name);
    }


    /**
     * @test
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
