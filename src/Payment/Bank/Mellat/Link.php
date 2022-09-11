<?php
declare(strict_types=1);

namespace App\Payment\Bank\Mellat;

use App\Payment\Bank\LinkInterface;
use App\Payment\BankCredential;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Payment\Bank\Mellat\Response;

class Link implements LinkInterface
{
    private readonly BankCredential $credential;
    private \SoapClient $client;
    private const WSDL_URI = 'https://old.banktest.ir/gateway/bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    public const PAYMENT_URI = 'https://old.banktest.ir/gateway/pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat';

    public function __construct(
        string $bankTerminalId,
        string $bankUserName,
        string $bankPassword,
        private UrlGeneratorInterface $router
    ) {
        $this->credential = new BankCredential(
            userName: $bankUserName,
            password: $bankPassword,
            terminalID: $bankTerminalId
        );

        $this->client = new \SoapClient(
            self::WSDL_URI,
            [
                'cache_wsdl' => 'WSDL_CACHE_NONE'
            ]
        );
    }

    public function payment(
        int $transactionId,
        int $userId,
        \DateTimeImmutable $createdAt,
        string $note,
        string $amount,
        string $callbackUrl,
    ): Response {
        $response = $this->client->bpPayRequest(
            [
                // terminal info
                'terminalId' => $this->credential->getTerminalId(),
                'userName' => $this->credential->getUserName(),
                'userPassword' => $this->credential->getPassword(),
                // request info
                'orderId' => $transactionId,
                'payerId' => $userId,
                'localDate' => $createdAt->format('Ymd'),
                'localTime' => $createdAt->format('His'),
                'additionalData' => $note,
                'amount' => $amount,
                'callBackUrl' => $callbackUrl,
            ]
        );

        return new Response($response->return);
    }

    public function verify(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): Response {
        $response = $this->client->bpVerifyRequest(
            [
                // terminal info
                'terminalId' => $this->credential->getTerminalId(),
                'userName' => $this->credential->getUserName(),
                'userPassword' => $this->credential->getPassword(),
                // request info
                'orderId' => $transactionId,
                'saleOrderId' => $paymentTransactionId,
                'saleReferenceId' => $bankReferenceId,
            ]
        );

        return new Response($response->return);
    }

    public function settle(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): Response {
        $response = $this->client->bpSettleRequest(
            [
                // terminal info
                'terminalId' => $this->credential->getTerminalId(),
                'userName' => $this->credential->getUserName(),
                'userPassword' => $this->credential->getPassword(),
                // request info
                'orderId' => $transactionId,
                'saleOrderId' => $paymentTransactionId,
                'saleReferenceId' => $bankReferenceId,
            ]
        );

        return new Response($response->return);
    }

    public function reversal(
        int $transactionId,
        int $paymentTransactionId,
        int $bankReferenceId,
    ): Response {
        $response = $this->client->bpReversalRequest(
            [
                // terminal info
                'terminalId' => $this->credential->getTerminalId(),
                'userName' => $this->credential->getUserName(),
                'userPassword' => $this->credential->getPassword(),
                // request info
                'orderId' => $transactionId,
                'saleOrderId' => $paymentTransactionId,
                'saleReferenceId' => $bankReferenceId,
            ]
        );

        return new Response($response->return);
    }

    public static function generateRedirectLink(string $bankToken): string
    {
        return static::PAYMENT_URI . '?RefId=' . $bankToken;
    }
}
