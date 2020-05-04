<?php

namespace Webleit\ZohoCrmApi\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self production()
 * @method static self all()
 * @method static self active()
 * @method static self deactive()
 * @method static self confirmed()
 * @method static self notConfirmed()
 * @method static self deleted()
 * @method static self activeConfirmed()
 * @method static self admin()
 * @method static self activeConfirmedAdmin()
 * @method static self current()
 *
 * @method static bool isProduction(string $value = null)
 * @method static bool isAll(string $value = null)
 * @method static bool isActive(string $value = null)
 * @method static bool isDeactive(string $value = null)
 * @method static bool isConfirmed(string $value = null)
 * @method static bool isNotConfirmed(string $value = null)
 * @method static bool isDeleted(string $value = null)
 * @method static bool isActiveConfirmed(string $value = null)
 * @method static bool isAdmin(string $value = null)
 * @method static bool isActiveConfirmedAdmin(string $value = null)
 * @method static bool isCurrent(string $value = null)
 */
class UserType extends Enum
{
    const MAP_VALUE = [
        'all' => 'AllUsers',
        'active' => 'ActiveUsers',
        'deactive' => 'DeactiveUsers',
        'confirmed' => 'ConfirmedUsers',
        'notConfirmed' => 'NotConfirmedUsers',
        'deleted' => 'DeletedUsers',
        'activeConfirmed' => 'ActiveConfirmedUsers',
        'admin' => 'AdminUsers',
        'activeConfirmedAdmin' => 'ActiveConfirmedAdmins',
        'current' => 'CurrentUser',
    ];
}
