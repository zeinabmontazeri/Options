<?php

namespace App\Payment\Service;

use App\Auth\AuthenticatedUser;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\PaymentCmd;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Repository\TransactionRepository;

class CheckoutService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AuthenticatedUser $authenticatedUser,
        private BankOperatonManager $operationManager,
    ) {
    }
    public function eventOrderCheckout(int $orderId)
    {
        // check if order id is valid, Order is in draft mode, and event started_at has not passed
        $order = $this->transactionRepository->isEventOrderPurchasable($orderId);
        if (is_null($order)) {
            throw new \Exception(sprintf('The order id(%d) is not purchasable.', $orderId));
        }

        if ($this->authenticatedUser->getUser() !== $order->getUser()) {
            throw new \Exception(sprintf('The order id(%d) is not purchasable.', $orderId));
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

    public function applyPaymentResponse(PaymentResponseCmd $cmd)
    {
        $response = $this->operationManager->run($cmd);
        // if ($response->getStatus() === TransactionStatusEnum::Success) {
            
        // }
    }
}
