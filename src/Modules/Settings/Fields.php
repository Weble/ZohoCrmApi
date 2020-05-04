<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class Fields extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'settings/fields';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\Field::class;
    }
}
