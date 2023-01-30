<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Mixins\ProvidesModules;
use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;

/**
 * @property-read SettingsModules\Modules $modules;
 * @property-read SettingsModules\Roles $roles;
 * @property-read SettingsModules\Profiles $profiles;
 * @property-read SettingsModules\Fields $fields;
 * @property-read SettingsModules\Layouts $layouts;
 * @property-read SettingsModules\RelatedLists $relatedLists;
 * @property-read SettingsModules\CustomViews $customViews;
 */
class Settings implements \Webleit\ZohoCrmApi\Contracts\ProvidesModules, \Webleit\ZohoCrmApi\Contracts\Module
{
    use ProvidesModules;

    protected Client $client;

    /**
     * @var array<string,class-string>
     */
    protected array $availableModules = [
        'modules' => Settings\Modules::class,
        'roles' => Settings\Roles::class,
        'profiles' => Settings\Profiles::class,
        'fields' => Settings\Fields::class,
        'layouts' => Settings\Layouts::class,
        'relatedlists' => Settings\RelatedLists::class,
        'customviews' => Settings\CustomViews::class,
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __get(string $name): ?\Webleit\ZohoCrmApi\Contracts\Module
    {
        return $this->createModule($name);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
