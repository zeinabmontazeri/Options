<?php

namespace App\Service;

use App\Entity\User;

class LoginCheckerService
{
    public function check(string $loginRole, array $userRole): bool
    {
        if (!array_key_exists($loginRole, User::ROLE_HIERARCHY))
            return false;

        if (count($userRole) === 0) {
            $userRole[] = User::ROLE_EXPERIENCER;
        }

        $userRoles = User::ROLE_HIERARCHY[strtoupper($userRole[0])];

        return in_array($loginRole, $userRoles);
    }
}
