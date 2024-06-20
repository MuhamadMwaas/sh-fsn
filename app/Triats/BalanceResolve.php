<?php

namespace App\Triats;

use App\Enum\TransferType;

trait BalanceResolve
{
    public static $buyType = [
        TransferType::buyCode,
        TransferType::buyInternationalsim,
    ];
    public function resolve(float $amount, TransferType $type, float $UserBalance, float $UserDebt)
    {
        (float)$BalanceOut = (float)$UserBalance;
        (float)$DebtOut = (float)$UserDebt;
        if ($type == TransferType::PaidCharge) {
            // زيادة الرصيد بقيمة الشحن
            (float)$BalanceOut = (float)$UserBalance + (float)$amount;
        } elseif ($type == TransferType::DebtCharge) {
            // زيادة الرصيد وزيادة قيمةالدين لانه شحن بالدين
            (float)$BalanceOut = (float)$UserBalance + (float)$amount;
            (float)$DebtOut = (float)$UserDebt + (float)$amount;
        } elseif ($type == TransferType::DebtRepayment) {
            if ((float)$UserDebt < $amount) {
                (float)$BalanceOut =  (float)$BalanceOut  + ((float)$amount - (float)$UserDebt);
                (float)$DebtOut  = 0;
            } else {
                (float)$DebtOut = (float)$UserDebt - (float)$amount;
            }
        } elseif (in_array($type, self::$buyType)) {
            (float)$BalanceOut = (float)$UserBalance - (float)$amount;
        } elseif ($type == TransferType::ChangeBalance) {
            (float)$BalanceOut = (float)$UserBalance;
            (float)$DebtOut = (float)$UserDebt;
        }

        return ['BalanceOut' => $BalanceOut, 'DebtOut' => $DebtOut];
    }
}
