<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Module;

class RelatedLists extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/related_lists';
    }

    public function getModelClassName(): string
    {
        return \Webleit\ZohoCrmApi\Models\Settings\RelatedList::class;
    }

    protected function getResourceKey(): string
    {
        return 'related_lists';
    }
}
