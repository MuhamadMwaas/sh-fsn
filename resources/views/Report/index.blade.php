@extends('layouts.app')

@section('content')
    <main id="main" class="main">

        {{-- <div class="container"> --}}
        <div class="pagetitle">
            <h1>تقرير المبيعات</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">الصفحة الرئيسية</a></li>
                    <li class=" active"> / تقرير المبياعات</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->


        <div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">General Form Elements</h5>
                    <!-- General Form Elements -->
                    <form>
                        <div class="row">
                            {{-- فلتر الرقم --}}
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-sm-3 mb-lg-0 mb-md-0 mb-3">
                                <label>تاريخ البدابة</label>
                                <input name="startDate" value="{{ $startDate }}" placeholder="تاريخ البداية"
                                    type="date" class="form-control">

                            </div>

                            {{-- فلتر التطبيق --}}
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-sm-3 mb-lg-0 mb-md-0 mb-3">
                                <label>تاريخ النهاية</label>
                                <input name="endDate" value="{{ $endDate }}" placeholder="تاريخ البداية" type="date"
                                    class="form-control">
                            </div>

                            <button type="submit" class="btn btn btn-outline-success mt-3">ابحث</button>
                        </div>
                    </form><!-- End General Form Elements -->
                </div>
            </div>
        </div>
        {{-- </div> --}}
        {{-- </div> --}}
        <section class="section dashboard">
            <div class="row">

                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">مجموع مبيعات الاكواد</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa fa-turkish-lira"></i>

                                </div>
                                <div class="ps-3">
                                    <h6>{{ $total_Sale_codes }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Revenue Card -->

                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">مجموع مبيعات تفعيل الخط الدولي</h5>
                            <div class="d-flex align-items-center">
                                <div
                                    class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger text-light">
                                    <i class="ri-coins-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $Internationalsim->count() * $price }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- User Info Card -->





            </div>
        </section>


        <div class="col-12">
            <div class="card recent-sales overflow-auto ">

                <div class="card-body">
                    <h5 class="card-title"><span> الرصيد | تفاصيل</span></h5>
                    <div class="table-responsive">
                        <table class="table table-  text-light" id="catTable">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <center> الفئة </center>
                                    </th>
                                    <th scope="col">
                                        <center>عدد المفاتيح المباعة</center>
                                    </th>
                                    <th scope="col">
                                        <center> السعر الاجمالي</center>
                                    </th>


                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>

                                        <td>
                                            <center>
                                                {{ $category->type }}
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                {{ $category->sold_codes_count }}
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                {{ $category->sold_codes_count * $category->price }}
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

    </main>
@endsection
@push('endjs')
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script> --}}
@endpush
@push('endjs')
    <script>
        const dataTable = new simpleDatatables.DataTable("#catTable", {
            perPage: 100,
            perPageSelect: [5, 10, 15, ["All", -1]],


        });
    </script>
@endpush
