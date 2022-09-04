<?php

namespace App\Auth;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AcceptableRoles extends IsGranted
{
    public function __construct(...$roles)
    {
        $acceptableRoles = RoleVoter::generateSequence($roles);

        parent::__construct(
            data: $acceptableRoles,
            subject: null,
            message: 'AUTHORIZATION_FAILED',
            statusCode: JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}
