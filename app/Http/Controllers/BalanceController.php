<?php

namespace App\Http\Controllers;

use App\Enum\TransferType;
use App\Models\Balance;
use App\Models\User;
use App\Triats\Charge;
use App\Triats\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class BalanceController extends Controller
{
    use Charge;
    use Transfer;
    // دالة لعرض جميع الأرصدة
    public function index()
    {
        if (Gate::allows('is-admin')) {
            // إذا كان المستخدم مديرًا
            $balances = Balance::all();
            return view('balances.index', ['balances' => $balances]);
        } else {
            // إذا كان المستخدم عاديًا
            // يمكنك توجيه المستخدم إلى صفحة أخرى أو عرض رسالة خطأ
            return abort(403, 'Unauthorized action.');
        }
    }
    //1
    public function showBalanceHistory($userId)
    {
        $user = Auth::user();
        if ($user->id == $userId || Gate::allows('is-admin')) {
            // Retrieve the user based on the provided user ID
            $user = User::findOrFail($userId);
            $balance = $user->Balance;
            $dept = $user->Debt;
            // Get balance transactions for the specified user
            $transactions = Balance::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // مصفوفة لتخزين البيانات
            $dataDebit = [];
            $dataCredit = [];
            $debitrepayment = [];
            $categories = [];

            // ملء المصفوفات
            foreach ($transactions as $transaction) {
                $categories[] = $transaction->created_at->format('Y-m-d H:i:s');
                $dataDebit[] = $transaction->debit_balance;
                $debitrepayment[] = $transaction->debit_repayment;
                $dataCredit[] = $transaction->credit_balance;
            }

            // احسب الرصيد الإجمالي في عمود debit_balance
            $debitBalance = array_sum($dataDebit);

            // احسب الرصيد الإجمالي في عمود credit_balance
            $creditBalance = array_sum($dataCredit);
            $userinfo = [
                'name' => $user->name,
                'email' => $user->email,
            ];
            // جمع الرصيد الإجمالي (متغير جديد)
            $totalBalance = $creditBalance - $debitBalance;
            return view('balances.show', [
                'user' => $userinfo,
                'userData' => $user,
                'transactions' => $transactions,
                'debitBalance' => $debitBalance,
                'creditBalance' => $creditBalance,
                'balance' => $balance,
                'dept' => $dept,
                'totalBalance' => $totalBalance,
                'dataDebit' => json_encode($dataDebit),
                'debitrepayment' => json_encode($debitrepayment),
                'dataCredit' => json_encode($dataCredit),
                'categories' => json_encode($categories),
            ]);
        } else {
            return abort(403, 'حاج تلعب........');
        }
    }
    public function showBalanceHistoryd()
    {
        $user = Auth::user();
        $userData = User::findOrFail(Auth::user()->id);
        $balance = $user->Balance;
        $dept = $user->Debt;
        // احصل على جميع عمليات الرصيد المرتبطة بالمستخدم مع البيانات التاريخية
        $transactions = Balance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // مصفوفة لتخزين البيانات
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

        // جمع الرصيد الإجمالي (متغير جديد)
        $totalBalance = $creditBalance - $debitBalance;

        return view('balances.show', [
            'transactions' => $transactions,
            'userData' => $userData,
            'debitBalance' => $debitBalance,
            'creditBalance' => $creditBalance,
            'balance' => $balance,
            'dept' => $dept,
            'totalBalance' => $totalBalance,
            'dataDebit' => json_encode($dataDebit),
            'dataCredit' => json_encode($dataCredit),
            'categories' => json_encode($categories),
        ]);
    }




    // دالة لعرض نموذج إضافة رصيد
    public function create()
    {
        $users = User::all();

        if (Gate::allows('is-admin')) {
            return view('balances.create', compact('users'));
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }


    public function store(Request $request)
    {
        // Validate user input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'balance_type' => 'required|in:credit_balance,debit_balance,debit_repayment',
            'amount' => 'required|numeric|min:0',
        ]);

        // Create a new Balance model instance
        $balance = new Balance([
            'user_id' => $request->input('user_id'),
        ]);
        // جلب المستخدم الذي يراد الشحن له 
        $user = User::findOrFail($request->user_id);

        // الحصول على الدين والرصيد الحالي للمستخدم
        $userBalance = $user->Balance;
        $userDebt = $user->Debt;


        // Update the balance based on the selected balance type
        if ($request->input('balance_type') === 'credit_balance') {
            $type = TransferType::PaidCharge;
            $balance->credit_balance += $request->input('amount');
        } elseif ($request->input('balance_type') === 'debit_repayment') {
            $type = TransferType::DebtRepayment;
            // Subtract the amount from the debit balance
            $balance->debit_balance += $request->input('amount');
        } else {
            $type = TransferType::DebtCharge;

            // Add the amount to the debit balance
            $balance->debit_balance -= $request->input('amount');
            $balance->credit_balance += $request->input('amount');
        }

        DB::beginTransaction();

        try {
            $this->chargeUser($user, $type, $request->amount);
            $this->createTransfer($user->id, $request->amount, $type, $userBalance, $userDebt, Auth::user()->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        
        // Calculate total balance
        $totalBalance = $balance->credit_balance - $balance->debit_balance;

        // Save the balance
        $balance->save();

        // Redirect the user to a relevant page with a success message
        return redirect()->route('balances.show', ['userId' => $balance->user_id])
            ->with('success', 'تمت إضافة الرصيد بنجاح.');
    }





    // دالة لعرض تفاصيل رصيد محدد
    public function show($id)
    {
        $balance = Balance::findOrFail($id);
        return view('balances.show', ['balance' => $balance]);
    }

    // دالة لعرض نموذج تعديل رصيد
    public function edit($userId)
    {
        if (Gate::allows('is-admin')) {
            $user = User::findOrFail($userId);
            $balance = Balance::where('user_id', $user->id)->first();

            return view('balances.edit', compact('user', 'balance'));
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }


    public function update(Request $request, Balance $balance)
    {
        // التحقق من صحة البيانات المُرسلة
        $request->validate([
            'credit_balance' => 'required|numeric',
        ]);

        // جلب أحدث سجل للرصيد بناءً على تاريخ الإضافة
        $latestBalanceRecord = Balance::where('user_id', $balance->user_id)
            ->latest('created_at')
            ->first();
        // ================================ ***************************** =============================================
        $user = User::findOrFail($request->input('UserId'));
        $amount = (float)$request->input('credit_balance') - (float) $user->Balance;
        DB::beginTransaction();

        try {
            $this->chargeUser($user, TransferType::ChangeBalance, $amount, (float)$request->input('credit_balance'), (float)$request->input('debit_balance'));
            $this->createTransfer(
                $user->id,
                $amount,
                TransferType::ChangeBalance,
                (float)$request->input('credit_balance'),
                (float)$request->input('debit_balance'),
                Auth::user()->id
            );
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }
        // ================================ ***************************** =============================================

        // التأكد من أن هناك سجل للرصيد
        if ($latestBalanceRecord) {
            // تحديث قيمة الرصيد في السجل الأحدث
            $latestBalanceRecord->update([
                'credit_balance' => $request->input('credit_balance'),
            ]);

            return redirect()->route('balances.show', ['userId' => $balance->user_id])
                ->with('success', 'Balance updated successfully!');
        } else {
            // في حالة عدم وجود سجلات للرصيد
            return redirect()->route('balances.show', ['userId' => $balance->user_id])
                ->with('error', 'No balance record found!');
        }
    }


    //    public function update(Request $request, Balance $balance)
    //    {
    //        // Validate the request data
    //        $request->validate([
    //            'credit_balance' => 'required|numeric',
    //            'debit_balance' => 'required|numeric',
    //        ]);

    //        // Update the balance model with the new data
    //        $balance->update([
    //            'credit_balance' => $request->input('credit_balance'),
    //            'debit_balance' => $request->input('debit_balance'),
    //        ]);

    //        return redirect()->route('balances.show', ['userId' => $balance->user_id])
    //            ->with('success', 'Balance updated successfully!');
    //    }



    // دالة لحذف الرصيد
    public function destroy($id)
    {
        if (Gate::allows('is-admin')) {
            $balance = Balance::findOrFail($id);
            $balance->delete();

            return redirect()->route('balances.index');
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }
}
