<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Models\Settings\Layout;
use Webleit\ZohoCrmApi\Modules\Module;

class Layouts extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/layouts';
    }

    public function getModelClassName(): string
    {
        return Layout::class;
    }
}
