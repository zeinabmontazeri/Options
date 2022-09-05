<?php

namespace App\Payment\Bank\Mellat;

enum ResponseEnum: int{
    case Success = 0;
    case InvalidCardNumber = 11;
    case InsufficientBalance = 12;
    case WrongPassword = 13;
    case TooManyRequestWithWrongPassword = 14;
    case InvalidCard = 15;
    case MaximumNumberOfWithdrawalRiched = 16;
    case UserCanceledTransaction = 17;
    case CardExpired = 18;
    case MaximumAmountOfWithdrawalRiched = 19;
    case InvalidCardProvider = 111;
    case CardProviderSwitchError = 112;
    case NoResponseFromCardProvider = 113;
    case CardHolderInUnauthorized = 114;
    case InvalidTerminal = 21;
    case SecurityError = 23;
    case TerminalCredentialIsInvalid = 24;
    case InvalidAmount = 25;
    case InvalidResponse = 31;
    case InvalidFormat = 32;
    case InvalidAccount = 33;
    case SystemError = 34;
    case InvalidDate = 35;
    case OrderIdIsDuplicated = 41;
    case SaleOrderIdNotFound = 42;
    case RepeatedVerifyRequest = 43;
    case VerifyRequestNotFound = 44;
    case TransactionIsSetteled = 45;
    case TransactionIsNotSetteled = 46;
    case SettleTransactionNotFound = 47;
    case TransactionIsReversed = 48;
    case InvalidInoviceNumber = 412;
    case InvalidPaymentId = 413;
    case InoviceOrganiztionIsInvalid = 414;
    case SessionIsExpired = 415;
    case DataRegisterationError = 416;
    case PayerIdIsInvalid = 417;
    case CustomerInfoIsProblematic = 418;
    case MaximumDataEntryLimitRiched = 419;
    case InvalidIP = 421;
    case TransactionIsDuplicated = 51;
    case ReferenceTransactionNotFound = 54;
    case InvalidTransaction = 55;
    case DisposalError = 61;
}