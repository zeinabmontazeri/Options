<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use App\Service\HostReportService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/hosts/')]
class ReportController extends AbstractController
{
    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('{host_id}/report', name: 'app_host_report', methods: 'GET')]
    #[ParamConverter('host', class: Host::class, options: ['id' => 'host_id'])]
    #[AcceptableRoles(User::ROLE_HOST, User::ROLE_ADMIN)]
    public function index(
        HostReportService    $hostReportService,
        Host                 $host, OrderRepository $orderRepository,
        ExperienceRepository $experienceRepository): JsonResponse
    {
        $res = $hostReportService->totalReport($orderRepository, $host, $experienceRepository);
        return $this->json([
            'data' => $res['data'],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }


    #[Route('{host_id}/experience/{experience_id}/report', name: 'app_host_experience_report', methods: 'GET')]
    #[ParamConverter('host', class: Host::class, options: ['id' => 'host_id'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function preciseReport(
        HostReportService $hostReportService,
        Host              $host, Experience $experience,
        OrderRepository   $orderRepository): JsonResponse
    {
        $res = $hostReportService->preciseReport($host, $experience, $orderRepository);
        return $this->json([
            'data' => $res['data'],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }
}