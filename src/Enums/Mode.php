<?php

namespace Webleit\ZohoCrmApi\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self production()
 * @method static self developer()
 * @method static self sandbox()
 *
 * @method static bool isProduction(string $value = null)
 * @method static bool isDeveloper(string $value = null)
 * @method static bool isSandbox(string $value = null)
 */
class Mode extends Enum
{
}
