<?php

namespace App\Http\Controllers;

use App\Enum\TransferType;
use App\Http\Requests\ImportCodesRequest;
use App\Models\Category;
use App\Models\Code;
use App\Models\Coderecord;
use App\Models\User;
use App\Triats\Charge;
use App\Triats\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\Gate;


class CodesController extends Controller
{
    use Charge;
    use Transfer;
    public function index()
    {
        // عرض كل الأكواد
        $codes = Code::all();
        return view('codes.index', compact('codes'));
    }

    public function show($id)
    {
        // عرض كود محدد
        $code = Code::findOrFail($id);
        return view('codes.show', compact('code'));
    }
    public function showCodesByCategory($categoryId)
    {
        // استرجاع الفئة المحددة
        $category = Category::findOrFail($categoryId);

        // الاكود الغير مشتراة
        $codes = Code::where('category_id', $categoryId)->whereDoesntHave('codeRecords')->get();

        // استرجاع الأكواد التي تنتمي إلى الفئة المحددة
        // $codes = Code::where('category_id', $categoryId)->get();
        // whereDoesntHave

        return view('codes.details', compact('category', 'codes'));
    }

    public function AdminshowCodesByCategory($categoryId)
    {
        // استرجاع الفئة المحددة
        $category = Category::where('id', $categoryId)->withCount(['codes' => function ($query) {
            $query->whereDoesntHave('codeRecords');
        }])->first();
        $category1 = Category::where('id', $categoryId)->first();
        $purchasedCodes = $category->codes()->whereHas('codeRecords')->get();
        $purchasedCodesCount = $purchasedCodes->count();
        // استرجاع الأكواد التي تنتمي إلى الفئة المحددة
        $codes = Code::where('category_id', $categoryId)->paginate(100);


        return view('codes.Admin_details', compact('category', 'codes', 'purchasedCodesCount'));
    }

    public function addToCodeRecord($codeId)
    {
        // احصل على الكود المحدد
        $code = Code::findOrFail($codeId);

        // احصل على المستخدم الحالي
        $user = User::findOrFail(auth()->user()->id);
        // احصل على الرصيد والدين الحالي للمستخدم
        $userBalance = $user->Balance;
        $userDebt = $user->Debt;

        $totalCreditBalance = $user->balance->sum('credit_balance');

        // التحقق من أن رصيد المستخدم كافٍ لشراء الكود  $totalCreditBalance >= $code->category->price
        if ($user->Balance >= $code->category->price && !$code->isRecorded()) {

            try {
                // إضافة الكود إلى سجل الأكواد
                CodeRecord::create([
                    'user_id' => $user->id,
                    'code_id' => $code->id,
                ]);

                // وضع الكود على اساس انه مشترى 
                $code->purchased = 1;
                $code->save();
                // خصم سعر الكود من رصيد المستخدم
                $user->balance->credit_balance -= $code->category->price;
                $user->balance->save();


                // ================================ ***************************** =============================================

                DB::beginTransaction();

                try {
                    $this->chargeUser($user, TransferType::buyCode, $code->category->price);
                    $this->createTransfer($user->id, $code->category->price, TransferType::buyCode, $userBalance, $userDebt, Auth::user()->id);
                    DB::commit();
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    dd($e);
                    // something went wrong
                }
                // ================================ ***************************** =============================================


                // عرض رسالة النجاح والرابط
                return redirect()->back()->with('success', 'تم شراء الكود بنجاح.')->with('code_purchased', true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'حدث خطأ أثناء عملية الشراء: ' . $e->getMessage());
            }
        } else {
            return redirect()->with('error', 'لا يوجد رصيد كافٍ لشراء هذا الكود.');
        }
    }



    public function create()
    {
        if (Gate::allows('is-admin')) {
            $categories = Category::all();

            if ($categories->isEmpty()) {
                return redirect()->route('categories.create')->with('error', 'يجب إضافة فئات أولاً.');
            }

            return view('codes.create', compact('categories'));
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }

    public function store(ImportCodesRequest $request)
    {
        $file = $request->file('file');
        $category_id = $request->input('category_id');
        $price = $request->input('price', 0);

        // التحقق من وجود الملف
        if (!$file) {
            return redirect()->route('codes.create')->with('error', 'يجب اختيار ملف الأكواد.');
        }

        // قراءة المحتوى من الملف النصي
        $content = file_get_contents($file->path());

        // تحويل المحتوى إلى مصفوفة من الأكواد
        $codes = explode("\n", $content);

        // حفظ الأكواد في جدول الأكواد
        foreach ($codes as $code) {
            Code::create([
                'code' => $code,
                'category_id' => $category_id,
                'price' => $price,
            ]);
        }

        return redirect()->route('codes.create')->with('success', 'تمت إضافة الأكواد بنجاح.');
    }



    public function destroy($id)
    {
        if (Gate::allows('is-admin')) {
            $code = Code::findOrFail($id);
            $code->delete();

            return redirect()->back()
                ->with('success', 'تم حذف الكود بنجاح');
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }

    public function deleteCode(Request $request)
    {
        if (Gate::allows('is-admin')) {
            $id = $request->input('id');
            $code
                = Code::where('id', $id);
            if ($code) {
                $code->delete();
                return redirect()->back()->with('success', 'تم حذف الكود بنجاح');
            }
        }
        return redirect()->route('site.index');
    }
}
