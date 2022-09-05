<?php

namespace App\Service;

use App\Auth\AuthenticatedUser;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\PaymentCmd;
use App\Payment\Event\PurchaseFailEvent;
use App\Payment\Event\PurchaseSuccessEvent;
use App\Repository\OrderRepository;
use App\Repository\TransactionRepository;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsEventListener(event: 'app.payment.order.success', method: 'purchaseSuccess')]
#[AsEventListener(event: 'app.payment.order.fail', method: 'purchaseFail')]
class OrderCheckoutService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private OrderRepository $orderRepository,
        private AuthenticatedUser $authenticatedUser,
        private BankOperatonManager $operationManager,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function checkout(int $orderId)
    {
        // check if order id is valid, Order is in draft mode, and event started_at has not passed
        $order = $this->orderRepository->isEventOrderPurchasable($orderId);
        if (is_null($order)) {
            throw new BadRequestHttpException(
                sprintf(
                    'The order id(%d) is not purchasable.',
                    $orderId,
                )
            );
        }

        if ($this->authenticatedUser->getUser() !== $order->getUser()) {
            throw new UnauthorizedHttpException(
                challenge: 'challenge',
                message: sprintf('The order id(%d) is not purchasable.', $orderId)
            );
        }

        // generate payment command
        $paymentCmd = new PaymentCmd(
            invoiceId: $orderId,
            userId: $order->getUser()->getId(),
            origin: TransactionOriginEnum::Order,
            amount: $order->getPayablePrice(),
            note: sprintf(
                'Payment for %s event which will be held at %s.',
                $order->getEvent()->getExperience()->getTitle(),
                $order->getEvent()->getStartsAt()->format('Y-m-d')
            )
        );

        // send payment command to Manager
        $paymentCmd = $this->operationManager->run($paymentCmd);

        // manage returned response
        if ($paymentCmd->getStatus() === TransactionStatusEnum::Success) {
            return $this->operationManager->generateRedirectLink($paymentCmd);
        } else {
            new HttpException(
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to initiate payment process.',
            );
        }
    }

    public function purchaseSuccess(PurchaseSuccessEvent $event): void
    {
        $orderPurchased = $this
            ->orderRepository
            ->setOrderAsCheckedOut($event->getInvoiceId());

        if (!$orderPurchased) {
            throw new HttpException(
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                sprintf(
                    'The order(%d) does not exist to be purchased.',
                    $event->getInvoiceId(),
                )
            );
        }
    }

    public function purchaseFail(PurchaseFailEvent $event): void
    {
        throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Payment Failed.');
    }
}
