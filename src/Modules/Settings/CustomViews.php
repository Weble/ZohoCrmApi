<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Models\Settings\CustomView;
use Webleit\ZohoCrmApi\Modules\Module;

class CustomViews extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/custom_views';
    }

    public function getModelClassName(): string
    {
        return CustomView::class;
    }

    protected function getResourceKey(): string
    {
        return 'custom_views';
    }
}
