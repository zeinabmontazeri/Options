<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Entity\Transaction;
use App\Entity\User;
use App\Payment\BankOperatonManager;
use App\Payment\Service\CheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private CheckoutService $checkoutCallbackService,
    )
    {
    }

    #[Route(
        '/api/v1/checkoutCallback/{callback_token}',
        name: 'app_payment_checkout_callback',
        methods: ['POST']
    )]
    #[AcceptableRoles(User::ROLE_GUEST)]
    public function checkoutCallback(
        Request             $request,
        string              $callback_token,
        BankOperatonManager $operationManager,
    ): JsonResponse
    {
        if (!Transaction::validateCallbackToken($callback_token)) {
            throw new BadRequestHttpException('Invalid callback Token');
        }

        $paymentResponseCmd = $operationManager::generatePaymentResponseCmd($request->getContent());

        $response = $this
            ->checkoutCallbackService
            ->applyPaymentResponse($paymentResponseCmd);

        return $this->json([
            'data' => [
                'BankReferenceId' => $response,
            ],
            'message' => 'Payment finished successfully.',
            'status' => 'success',
        ], Response::HTTP_OK);
    }
}
