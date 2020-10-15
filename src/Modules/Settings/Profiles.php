<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Models\Settings\Profile;
use Webleit\ZohoCrmApi\Modules\Module;

class Profiles extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/profiles';
    }

    public function getModelClassName(): string
    {
        return Profile::class;
    }
}
