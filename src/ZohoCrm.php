<?php

namespace Webleit\ZohoCrmApi;

use Tightenco\Collect\Support\Collection;
use Webleit\ZohoCrmApi\Mixins\ProvidesModules;
use Webleit\ZohoCrmApi\Models\Settings\Module;
use Webleit\ZohoCrmApi\Modules;
use Webleit\ZohoCrmApi\Modules\Records;

/**
 * @property-read Modules\Settings $settings
 * @property-read Modules\Users $users
 * @property-read Modules\Org $org
 * @property-read Records $records
 * @property-read Modules\Leads $leads
 */
class ZohoCrm implements Contracts\ProvidesModules
{
    use ProvidesModules;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $totals = [];

    /**
     * @var array
     */
    protected $availableModules = [
        'settings' => Modules\Settings::class,
        'users'    => Modules\Users::class,
        'org'      => Modules\Org::class,
        'records'  => Records::class,
        'leads'    => Modules\Leads::class
    ];

    /**
     * @var Collection
     */
    protected $apiModules;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception\ApiError
     * @throws Exception\GrantCodeNotSetException
     */
    public function __get($name)
    {
        $module = $this->createModule($name);

        if ($module) {
            return $module;
        }

        return $this->createRecordsModule($name);
    }

    /**
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function createRecordsModule(string $name): ?Records
    {
        $modules = $this->getApiModules();
        if ($modules->has($name)) {
            /** @var Records $recordsModule */
            return new Records($this->getClient(), $name);
        }

        return null;
    }

    public function getApiModules(): Collection
    {
        if (!$this->apiModules) {
            $this->apiModules = $this->settings->modules->getList()->mapWithKeys(function (Module $module) {
                return collect([strtolower($module->module_name) => $module]);
            });
        }

        return $this->apiModules;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
