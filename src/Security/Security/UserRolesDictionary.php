<?php

namespace App\Security\Security;

class UserRolesDictionary
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const validRoles = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public static function areValid(array $roles): bool
    {
        foreach ($roles as $role) {
            if (!in_array($role, self::validRoles)) {
                return false;
            }
        }
        return true;
    }
}
