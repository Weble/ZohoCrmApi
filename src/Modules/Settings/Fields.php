<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Models\Settings\Field;
use Webleit\ZohoCrmApi\Modules\Module;

class Fields extends Module
{
    public function getUrlPath(): string
    {
        return 'settings/fields';
    }

    public function getModelClassName(): string
    {
        return Field::class;
    }
}
