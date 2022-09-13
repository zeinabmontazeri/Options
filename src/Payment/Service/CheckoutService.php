<?php

namespace App\Payment\Service;

use App\Entity\Enums\TransactionStatusEnum;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Payment\Event\PurchaseFailEvent;
use App\Payment\Event\PurchaseSuccessEvent;
use App\Repository\TransactionRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckoutService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private BankOperatonManager $operationManager,
        private EventDispatcherInterface $dispatcher,
    ) {
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

            return $this
                ->transactionRepository
                ->find($paymentResponseCmd->getTransactionId())
                ->getBankReferenceId();
        } elseif ($paymentResponseCmd->getStatus() === TransactionStatusEnum::Failure) {
            $purchaseFailEvent = new PurchaseFailEvent(
                origin: $paymentCmd->getOrigin(),
                invoiceId: $paymentCmd->getInvoiceId(),
            );
            $this->dispatcher->dispatch(
                event: $purchaseFailEvent,
                eventName: sprintf('app.payment.%s.fail', $origin)
            );

            return null;
        }
    }

    private function generateEventNameFromEnum(string $enumValue)
    {
        $name = substr($enumValue, strlen('ORIGIN_'));
        return strtolower($name);
    }
}
