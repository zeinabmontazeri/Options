<?php

namespace App\Controller;

use App\Auth\AcceptableRoles;
use App\Entity\Transaction;
use App\Entity\User;
use App\Payment\BankOperatonManager;
use App\Payment\Service\CheckoutService;
use App\Service\OrderCheckoutService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private OrderCheckoutService $orderCheckoutService,
        private CheckoutService $checkoutCallbackService,
    ) {
    }

    #[Route(
        '/api/v1/checkoutCallback/{callback_token}',
        name: 'app.payment.checkout_callback',
        methods: ['POST']
    )]
    public function checkoutCallback(
        Request $request,
        string $callback_token,
        BankOperatonManager $operationManager,
    ) {
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
            'code' => JsonResponse::HTTP_OK,
        ]);
    }
}
