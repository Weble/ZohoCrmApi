<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Models\User;
use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 *
 * @property-read SettingsModules\Modules $modules;
 */
class Users extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'users';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return User::class;
    }
}
