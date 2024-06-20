<?php

namespace App\Triats;

use App\Enum\TransferType;
use App\Models\Transfers_History;
use App\Models\User;
use PhpParser\Node\Expr\Cast\Double;

trait Transfer
{
    use BalanceResolve;

    public static $buyType = [
        TransferType::buyCode,
        TransferType::buyInternationalsim,
    ];
    public function createTransfer($user_id, float $amount, TransferType $type, float $UserBalance, float $UserDebt, $Owner_id)
    {
        (float)$BalanceAfter = $UserBalance;
        (float)$DebtAfter = $UserDebt;

        ['BalanceOut' => $BalanceAfter, 'DebtOut' => $DebtAfter] = $this->resolve($amount, $type, $UserBalance, $UserDebt);
        $Transfers_History = new Transfers_History();
        $Transfers_History->user_id = $user_id;
        $Transfers_History->Owner_id = $Owner_id;
        $Transfers_History->type = $type->value;
        $Transfers_History->amount = $amount;
        $Transfers_History->Balance_after = $BalanceAfter;
        $Transfers_History->Debt_after = $DebtAfter;

        $Transfers_History->save();
    }
}
