<?php

namespace App\Payment\Service;

use App\Auth\AuthenticatedUser;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\PaymentCmd;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Payment\Event\PurchaseFailEvent;
use App\Payment\Event\PurchaseSuccessEvent;
use App\Repository\TransactionRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckoutService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AuthenticatedUser $authenticatedUser,
        private BankOperatonManager $operationManager,
        private EventDispatcherInterface $dispatcher,
    ) {
    }
    public function eventOrderCheckout(int $orderId)
    {
        // check if order id is valid, Order is in draft mode, and event started_at has not passed
        $order = $this->transactionRepository->isEventOrderPurchasable($orderId);
        if (is_null($order)) {
            throw new BadRequestHttpException(sprintf('The order id(%d) is not purchasable.', $orderId));
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
            new \Exception('Failed to initiate payment process.');
        }
    }

    public function applyPaymentResponse(PaymentResponseCmd $paymentResponseCmd): ?int
    {
        $paymentResponseCmd = $this->operationManager->run($paymentResponseCmd);
        $paymentCmd = $this
            ->operationManager
            ->getPaymentFromPaymentResponse($paymentResponseCmd);

        $origin = $this->generateEventNameFromEnum($paymentCmd->getOrigin()->value);

        if ($paymentResponseCmd->getStatus() === TransactionStatusEnum::Success) {

            $purchaseSuccessEvent = new PurchaseSuccessEvent(
                origin: $paymentCmd->getOrigin(),
                invoiceId: $paymentCmd->getInvoiceId(),
            );
            $this->dispatcher->dispatch(
                event: $purchaseSuccessEvent,
                eventName: sprintf('app.payment.%s.success', $origin)
            );
        } elseif ($paymentResponseCmd->getStatus() === TransactionStatusEnum::Failure) {
            $purchaseFailEvent = new PurchaseFailEvent(
                origin: $paymentCmd->getOrigin(),
                invoiceId: $paymentCmd->getInvoiceId(),
            );
            $this->dispatcher->dispatch(
                event: $purchaseFailEvent,
                eventName: sprintf('app.payment.%s.fail', $origin)
            );
        }

        return $paymentResponseCmd->getBankReferenceId();
    }

    private function generateEventNameFromEnum(string $enumValue)
    {
        $name = substr($enumValue, strlen('ORIGIN_'));
        return strtolower($name);
    }
}
