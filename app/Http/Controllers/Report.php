<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Internationalsim;
use Illuminate\Http\Request;

class Report extends Controller
{


    // ==================================================== report page=====================================
    /**
     * صفحة عرض التقارير اليومية
     */
    public function index(Request $request)
    {
        $request->validate([
            'startDate' => 'nullable|date',
            'startDate' => 'nullable|date',
        ]);

        $startDate = $request->input('startDate') ?? date_format(now()->subDays(1), 'Y-m-d');
        $endDate = $request->input('endDate') ?? date_format(now(), 'Y-m-d');
        $categories = Category::withCount(['codes as sold_codes_count' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('codeRecords', function ($query) use ($startDate, $endDate) {
                // $query->whereBetween('created_at', [$startDate, $endDate]);
                $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            });
        }])->get()->map(
            function ($category) {
                // حساب مجموع المبيعات
                $category->total_sales = $category->sold_codes_count * $category->price;
                return $category;
            }
        );

        $total_Sale_codes = $categories->sum('total_sales');

        $Internationalsim = Internationalsim::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();
        $price = 170;
        return view('Report.index', compact('categories', 'startDate', 'endDate', 'Internationalsim', 'total_Sale_codes', 'price'));
    }
}
