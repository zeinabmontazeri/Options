<?php

namespace App\Service;

use App\Entity\User;

class LoginCheckerService
{
    public function check(string $loginRole, string|array $userRole): bool
    {
        if (!array_key_exists($loginRole, User::ROLE_HIERARCHY))
            return false;

        if (is_array($userRole)) {
            if (count($userRole) == 0) {
                $userRole[] = User::ROLE_EXPERIENCER;
            }

            $userRole = array_map('strtoupper', $userRole);

            // expand roles
            $expanded_roles = [];
            foreach ($userRole as $role) {
                $expanded_roles = array_merge($expanded_roles, User::ROLE_HIERARCHY[$role]);
            }
            $userRole = array_unique($expanded_roles);
        } elseif (is_string($userRole)) {
            $userRole = User::ROLE_HIERARCHY[strtoupper($userRole)];
        }

        if (!in_array($loginRole, $userRole))
            return false;

        return true;
    }
}
