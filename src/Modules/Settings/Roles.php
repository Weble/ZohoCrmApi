<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class Roles extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'settings/roles';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\Role::class;
    }
}
