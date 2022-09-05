<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Auth\AuthenticatedUser;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\ExperienceRepository;
use App\Repository\HostRepository;
use App\Repository\OrderRepository;
use App\Service\HostReportService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/hosts')]
class ReportController extends AbstractController
{
    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/report', name: 'app_host_report', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function index(
        HostReportService    $hostReportService,
        OrderRepository      $orderRepository,
        ExperienceRepository $experienceRepository,
        HostRepository       $hostRepository
    ): JsonResponse
    {
        $res = $hostReportService->totalReport($orderRepository, $experienceRepository, $hostRepository);
        return $this->json([
            'data' => $res['data'],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }


    #[Route('/experiences/{experience_id}/report', name: 'app_host_experience_report', methods: 'GET')]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function preciseReport(
        HostReportService $hostReportService,
        HostRepository    $hostRepository,
        Experience        $experience,
        OrderRepository   $orderRepository): JsonResponse
    {
        $res = $hostReportService->preciseReport($experience, $orderRepository, $hostRepository);
        return $this->json([
            'data' => $res['data'],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }
}