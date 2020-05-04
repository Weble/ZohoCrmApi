<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Mixins\ProvidesModules;
use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;

/**
 * Class Settings
 * @package Webleit\ZohoCrmApi\Modules
 *
 * @property-read SettingsModules\Modules $modules;
 * @property-read SettingsModules\Roles $roles;
 * @property-read SettingsModules\Profiles $profiles;
 * @property-read SettingsModules\Fields $fields;
 * @property-read SettingsModules\Layouts $layouts;
 * @property-read SettingsModules\RelatedLists $relatedLists;
 * @property-read SettingsModules\CustomViews $customViews;
 */
class Settings implements \Webleit\ZohoCrmApi\Contracts\ProvidesModules
{
    use ProvidesModules;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $availableModules = [
        'modules' => Settings\Modules::class,
        'roles' => Settings\Roles::class,
        'profiles' => Settings\Profiles::class,
        'fields' => Settings\Fields::class,
        'layouts' => Settings\Layouts::class,
        'relatedlists' => Settings\RelatedLists::class,
        'customviews' => Settings\CustomViews::class,
    ];

    /**
     * Settings constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Proxy any module call to the right api call
     * @param $name
     * @return Module
     */
    public function __get($name)
    {
        return $this->createModule($name);
    }
}
