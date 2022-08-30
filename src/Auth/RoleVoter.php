<?php

namespace App\Auth;

use App\Entity\User;
use App\Exception\AuthorizationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RoleVoter implements VoterInterface
{
    private const SIGN = 'ACCEPTABLE_ROLES:';

    public function __construct(private JWTTokenManagerInterface $tokenManager)
    {
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if (!isset($attributes[0])) {
            return self::ACCESS_ABSTAIN;
        }

        $acceptableRoles = self::parseSequence($attributes[0]);
        if (count($acceptableRoles) === 0) {
            return self::ACCESS_ABSTAIN;
        }

        if ($token instanceof NullToken) {
            return in_array(User::ROLE_GUEST, $acceptableRoles)
                ? self::ACCESS_GRANTED
                : self::ACCESS_DENIED;
        }

        $loginRole = strtoupper(($this->tokenManager->decode($token))['roles']);

        if (in_array($loginRole, $acceptableRoles)) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }

    public static function generateSequence($roles): string
    {
        return self::SIGN . implode('|', self::checkRoles($roles));
    }

    private static function parseSequence($seq): array
    {
        $patternMatch = preg_match('/' . self::SIGN . '(?:[A-Z_]+\|)*(?:[A-Z_]+)/', $seq);
        if (!$patternMatch) {
            return [];
        }

        $acceptableRoles = explode('|', substr($seq, strlen(self::SIGN)));
        $acceptableRoles = self::checkRoles($acceptableRoles);

        return $acceptableRoles;
    }

    private static function checkRoles($roles): array
    {
        $checkedRoles = array_reduce($roles, function ($acc, $role) {
            if (in_array(strtoupper($role), User::ROLE_ALL)) {
                $acc[] = strtoupper($role);
                return $acc;
            } else {
                throw new AuthorizationException('INVALID_ROLE_ATTRIBUTE_ON_CONTROLLER', 500);
            }
        }, []);

        return array_unique($checkedRoles);
    }
}
