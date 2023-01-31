<?php

namespace Webleit\ZohoCrmApi;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Mixins\ProvidesModules;
use Webleit\ZohoCrmApi\Models\Settings\Module;
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

    protected Client $client;

    /**
     * @var class-string[]
     */
    protected array $availableModules = [
        'settings' => Modules\Settings::class,
        'users' => Modules\Users::class,
        'org' => Modules\Org::class,
        'records' => \Webleit\ZohoCrmApi\Modules\Records::class,
        'leads' => Modules\Leads::class,
    ];

    /** @var Collection<string,Module>|null  */
    protected ?Collection $apiModules;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __get(string $name): ?Modules\Module
    {
        /** @var Module|null $module */
        $module = $this->createModule($name);

        if (!$module) {
            $module = $this->createRecordsModule($name);
        }

        /** @var Module|null $module */
        return $module;
    }

    public function createRecordsModule(string $name): Records
    {
        return new Records($this->getClient(), $name);
    }

    /**
     * @return Collection<string,Module>
     * @throws Exception\InvalidResourceKey
     */
    public function getApiModules(): Collection
    {
        if (! $this->apiModules) {
            $this->apiModules = $this->settings->modules->getList()->mapWithKeys(function ($module) {
                /** @var Module $module */
                return [strtolower($module->api_name) => $module];
            });
        }

        return $this->apiModules;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
