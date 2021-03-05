<?php

namespace Webleit\ZohoCrmApi\Enums;

abstract class UserType
{
    public const ALL = 'AllUsers';
    public const ACTIVE = 'ActiveUsers';
    public const INACTIVE = 'DeactiveUsers';
    public const CONFIRMED = 'ConfirmedUsers';
    public const NOT_CONFIRMED = 'NotConfirmedUsers';
    public const DELETED = 'DeletedUsers';
    public const ACTIVE_CONFIRMED = 'ActiveConfirmedUsers';
    public const ADMIN = 'AdminUsers';
    public const ACTIVE_CONFIRMED_ADMIN = 'ActiveConfirmedAdmins';
    public const CURRENT = 'CurrentUser';
}
