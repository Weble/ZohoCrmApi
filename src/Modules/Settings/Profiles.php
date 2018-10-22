<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;
use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class Profiles extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'settings/profiles';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\Profile::class;
    }
}