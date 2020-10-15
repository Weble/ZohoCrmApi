<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Models\Settings\Role;
use Webleit\ZohoCrmApi\Modules\Module;

class Roles extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/roles';
    }

    public function getModelClassName(): string
    {
        return Role::class;
    }
}
