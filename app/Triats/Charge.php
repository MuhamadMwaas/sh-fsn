<?php

namespace App\Triats;

use App\Enum\TransferType;
use App\Models\Balance;
use App\Models\User;

trait Charge
{
    use BalanceResolve;
    public function chargeUser(User &$user, TransferType $type, float $amount, float $Balance = null, float $Debt = null)
    {
        // // شحن مدفوع
        // if ($type == TransferType::PaidCharge) {
        //     // زيادة الرصيد بقيمة الشحن
        //     (float)$user->Balance = (float)$user->Balance + (float)$amount;
        // } elseif ($type == TransferType::DebtCharge) {
        //     // زيادة الرصيد وزيادة قيمةالدين لانه شحن بالدين
        //     (float)$user->Balance = (float)$user->Balance + (float)$amount;
        //     (float)$user->Debt = (float)$user->Debt + (float)$amount;
        // } elseif ($type == TransferType::DebtRepayment) {
        //     if ((float)$user->Debt < $amount) {
        //         (float)$user->Balance =  (float)$user->Balance  + ((float)$amount - (float)$user->Debt);
        //         (float)$user->Debt  = 0;
        //     } else {
        //         (float)$user->Debt = (float)$user->Debt - (float)$amount;
        //     }
        // }
        if (is_null($Balance)) {
            $Balance = $user->Balance ?? 0;
        }
        if (is_null($Debt)) {
            $Debt = $user->Debt ?? 0;
        }

        $data = $this->resolve($amount, $type, $Balance, $Debt);
        $user->Balance = $data['BalanceOut'];
        $user->Debt = $data['DebtOut'];
        $user->save();
        return $data;
    }
}
