<?php

namespace App\Auth;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AcceptableRoles extends IsGranted
{
    public function __construct(...$roles)
    {
        $acceptableRoles = RoleVoter::generateSequence($roles);

        parent::__construct(
            data: $acceptableRoles,
            subject: null,
            message: 'AUTHORIZATION FAILED.',
            statusCode: Response::HTTP_FORBIDDEN
        );
    }
}
