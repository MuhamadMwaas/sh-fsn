<?php

namespace App\Http\Controllers;

use App\Enum\TransferType;
use App\Models\Balance;
use App\Models\User;
use App\Triats\BalanceResolve;
use App\Triats\Charge;
use App\Triats\Transfer;
use Illuminate\Http\Request;

class Update extends Controller
{
    use Transfer;
    use Charge;
    public function index()
    {

        $flage = env('balance', 0);
        if ($flage == 1) {
            $users = User::all();
            foreach ($users as $user) {

                $transactions = Balance::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                unset($dataDebit);
                unset($dataCredit);
                unset($categories);

                $dataDebit = [];
                $dataCredit = [];
                $categories = [];

                // ملء المصفوفات
                foreach ($transactions as $transaction) {
                    $categories[] = $transaction->created_at->format('Y-m-d H:i:s');
                    $dataDebit[] = $transaction->debit_balance;
                    $dataCredit[] = $transaction->credit_balance;
                }

                // احسب الرصيد الإجمالي في عمود debit_balance
                $debitBalance = array_sum($dataDebit);
                // احسب الرصيد الإجمالي في عمود credit_balance
                $creditBalance = $transactions->isEmpty() ? 0 : array_sum($dataCredit);

                $user->Balance = $creditBalance;
                $user->Debt = abs($debitBalance);

                $user->save();
            }
        }
    }

    public function test()
    {
        $user = User::find(24);
        dd($this->chargeUser($user, TransferType::DebtRepayment, 60));
    }
}
