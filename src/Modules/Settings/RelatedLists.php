<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class RelatedLists extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'settings/related_lists';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\RelatedList::class;
    }

    /**
     * @return string
     */
    protected function getResourceKey()
    {
        return 'related_lists';
    }
}
