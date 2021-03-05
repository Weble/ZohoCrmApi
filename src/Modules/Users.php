<?php

namespace Webleit\ZohoCrmApi\Modules;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Enums\UserType;
use Webleit\ZohoCrmApi\Models\User;
use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;

/**
 * @property-read SettingsModules\Modules $modules;
 */
class Users extends Module
{
    public function getUrlPath(): string
    {
        return 'users';
    }

    public function getModelClassName(): string
    {
        return User::class;
    }

    public function ofType(string $type): Collection
    {
        return $this->getList([
            'type' => $type,
        ]);
    }

    public function current(): User
    {
        return $this->ofType(UserType::CURRENT)->first();
    }
}
