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
    ): ResponseInterface;

    public function verify(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): ResponseInterface;

    public function settle(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): ResponseInterface;

    public function reversal(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): ResponseInterface;

    public static function generateRedirectLink(string $bankToken): string;
}
