<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;
use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class CustomViews extends Module
{
    /**
     * @return string
     */
    public function getUrlPath ()
    {
        return 'settings/custom_views';
    }

    /**
     * @return string
     */
    public function getModelClassName ()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\CustomView::class;
    }

    /**
     * @return string
     */
    protected function getResourceKey ()
    {
        return 'custom_views';
    }


}