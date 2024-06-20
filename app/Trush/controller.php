<?php 

  // if ($type == TransferType::PaidCharge) {
        //     (float)$BalanceAfter = (float)$UserBalance + (float)$amount;
        //     (float) $DebtAfter = $UserDebt;
        // } elseif ($type == TransferType::DebtCharge) {
        //     (float)$BalanceAfter = (float)$UserBalance + (float)$amount;
        //     (float)$DebtAfter = (float)$UserDebt + (float)$amount;
        // } elseif ($type == TransferType::DebtRepayment) {
        //     if ($UserDebt < $amount) {
        //         (float)$BalanceAfter = (float)$UserBalance + ((float)$amount - (float)$UserDebt);
        //         (float)$DebtAfter = 0;
        //     } else {
        //         (float)$DebtAfter = (float)$UserDebt - (float)$amount;
        //         (float)$BalanceAfter = $UserBalance;
        //     }
        // } elseif (in_array($type, self::$buyType)) {
        //     (float)$BalanceAfter = (float)$UserBalance - (float)$amount;
        //     (float)$DebtAfter = $UserDebt;
        // }