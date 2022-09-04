<?php

namespace App\Controller;

use App\Auth\AcceptableRoles;
use App\Entity\Transaction;
use App\Entity\User;
use App\Payment\BankOperatonManager;
use App\Payment\Service\CheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private CheckoutService $service
    ) {
    }

    #[Route(
        '/api/v1/orders/{order_id<\d+>}/checkout',
        name: 'app.payment.checkout'
    )]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function eventOrderCheckout(int $order_id)
    {
        $redirectLink = $this->service->eventOrderCheckout($order_id);
        return $this->redirect($redirectLink);
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

        $response = $this->service->applyPaymentResponse($paymentResponseCmd);
        if (is_null($response)) {
            throw new HttpException('Payment Failed.');
        } else {
            $this->json([
                'data' => [
                    'BankReferenceId' => $response,
                ],
                'message' => 'Payment finished successfully.',
                'status' => 'success',
                'code' => 200,
            ]);
        }
    }
}
