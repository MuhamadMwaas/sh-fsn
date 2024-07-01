@extends('layouts.app')

@section('content')
    <main id="main" class="main">

        <div class="container">
            <div class="pagetitle">
                <h1>تفاصيل الرصيد</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">الصفحة الرئيسية</a></li>
                        <li class=" active"> / تفاصيل الرصيد</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->

            <section class="section dashboard">
                <div class="row">

                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title"> الرصيد الفعال </h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fa fa-turkish-lira"></i>

                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $balance }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Revenue Card -->

                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title"> رصيد الدين </h5>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger text-light">
                                        <i class="fa fa-turkish-lira"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $dept }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- User Info Card -->
                    @can('is-admin')
                        <div class="col-xxl-4 col-md-6">
                            <div class="card user-info-card">
                                <div class="card-body">
                                    <h5 class="card-title">معلومات المستخدم</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ps-3">
                                            @foreach ($user as $info)
                                                <h6>{{ $info }}</h6>
                                                <!-- أضف أي معلومات إضافية ترغب في عرضها هنا -->
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @if (Auth::user() && Auth::user()->role_id == 2)
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title"> طرق الدفع لدينا </h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary text-light">
                                            <i class="bi bi-cash-coin"></i>
                                        </div>
                                        <div class="ps-3 ss">
                                            <a href="{{ url('/payment') }}">
                                                <p>لدينا العديد من طرق الدفع المتاحة لدينا</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Revenue Card -->
                    @endif
                    <!-- End User Info Card -->


                    <!-- Reports -->
                    <div class="col-12">
                        <div class="card">
                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">الرصيد <span>/ الحالي</span></h5>

                                <!-- Line Chart -->
                                <div id="reportsChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [

                                                {
                                                    name: 'الرصيد الفعال',
                                                    data: {!! $dataCredit !!}
                                                }, {
                                                    name: 'رصيد الدين',
                                                    data: {!! $dataDebit !!}
                                                }
                                            ],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: {!! $categories !!}
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy HH:mm'
                                                },
                                            }
                                        }).render();
                                    });
                                </script>
                                <!-- End Line Chart -->

                            </div>

                        </div>
                    </div><!-- End Reports -->

                </div>
            </section>


            <div class="col-12">
                <div class="card recent-sales overflow-auto ">

                    <div class="card-body">
                        <h5 class="card-title"><span> الرصيد | تفاصيل</span></h5>
                        <div class="table-responsive">
                            <table class="table table- datatable text-light">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <center>نوع العملية</center>
                                        </th>
                                        <th scope="col">
                                            <center>قيمة العملية</center>
                                        </th>
                                        <th scope="col">
                                            <center>تاريخ العملية</center>
                                        </th>
                                        <th scope="col">
                                            <center>الرصيد بعد العملية</center>
                                        </th>
                                        <th scope="col">
                                            <center>الدين بعد العملية</center>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userData->transfer_History as $transaction)
                                        <tr>

                                            <td>
                                                <center
                                                    class="{{ App\Enum\TransferType::tryFrom($transaction->type)->class() }}">
                                                    {{ App\Enum\TransferType::tryFrom($transaction->type)->description() }}
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    {{ $transaction->amount }}
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    {{ $transaction->created_at }}
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    {{ $transaction->Balance_after }}
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    {{ $transaction->Debt_after }}
                                                </center>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- السج --}}
            <div class="accordion" id="accordionExample">
                {{-- سجل التحويلات المالية --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            سجل مشتريات الاكواد
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        {{-- <div class="card-header">
                                        <h5 class="card-title">سجل التحويلات المالية </h5>
                                    </div> --}}
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-nowrap mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>الفئة</th>
                                                            <th>المفتاح</th>
                                                            <th>تاريخ الشراء</th>
                                                            <th>السعر</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($codes as $code)
                                                            <tr>
                                                                <td>{{ $code->category->type }}</td>
                                                                <td>{{ $code->code }}</td>
                                                                <td>{{ $code->purchaseDate() }}</td>
                                                                <td>{{ $code->category->price }}</td>
                                                            </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="d-flex">
                                            {!! $codes->links() !!}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- سجل طلبات الشحن --}}
                {{-- <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            سجل طلبات الشحن
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">سجل التحويلات المالية </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-nowrap mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>التطبيق</th>
                                                            <th>اسم عنصر الشحن</th>
                                                            <th>كمية عنصر الشحن</th>
                                                            <th>السعر الافرادي</th>
                                                            <th>ايدي الحساب</th>
                                                            <th>العدد</th>
                                                            <th>الحالة</th>
                                                            <th>المجيب</th>
                                                            <th>تاريخ الانشاء</th>
                                                            <th>تاريخ الاستجابة</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> --}}

                {{-- سجل المفاتحح المشتراة --}}
                {{-- <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            المفاتيح المشتراة
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">سجل التحويلات المالية </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-nowrap mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>التطبيق</th>
                                                            <th>المفتاح</th>
                                                            <th>تاريخ الشراء</th>
                                                            <th>السعر</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>



                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> --}}
            </div>

        </div>
    </main>
@endsection
@push('endjs')
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script> --}}
@endpush
