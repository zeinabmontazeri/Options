<?php

namespace App\Payment\Bank;

interface LinkInterface
{
    public function payment(
        int $transactionId,
        int $userId,
        \DateTimeImmutable $createdAt,
        string $note,
        string $amount,
        string $callbackUrl,
    );

    public function verify(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    );

    public function settle(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    );

    public function inquery(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    );

    public function reversal(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    );

    public static function generateRedirectLink(string $bankToken): string;
}
