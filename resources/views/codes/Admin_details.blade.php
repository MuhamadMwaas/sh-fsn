@extends('layouts.app')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>أكواد فئة <span class="text-info">{{ $category->type }}</span> </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/categories') }}">الصفحة الرئيسية</a></li>
                    <li><span>/{{ $category->type }} / الاكواد</span></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row align-items-top">
                {{-- ترتيب الأكواد بشكل عشوائي --}}
                @php
                    $shuffledCodes = $codes->shuffle();
                @endphp

                {{-- عرض أول كود غير مسجل --}}

                @foreach ($shuffledCodes as $index => $code)
                    @if (true)
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $category->type }} <a
                                            href="{{ route('categories.edit', $category->id) }}"
                                            class="btn btn-outline-success custom-btn">
                                            <i class="bi bi-pencil-square"></i>
                                        </a></h5>

                                    <span type="button" class="btn btn-primary mb-2">
                                        عدد الاكواد المتوفرة <span
                                            class="badge bg-white text-primary">{{ $category->codes_count }}</span>
                                    </span>
                                    <span type="button" class="btn btn-danger mb-2">
                                        عدد الاكواد المباعة <span
                                            class="badge bg-white text-primary">{{ $purchasedCodesCount }}</span>
                                    </span>
                                    <br>
                                    <span>السعر : </span>
                                    <span class="card-text">{{ $category->price }}<span><i
                                                class="fa fa-turkish-lira"></i></span></span>
                                    @if (Auth::user() && Auth::user()->role_id == 2)
                                        @if ($category->price > $creditBalanceUser)
                                            <h5 style="color: red;">ليس لديك ما يكفي من الرصيد لشراء هذا الكود</h5>
                                        @elseif($code->isRecorded())
                                            <h5 style="color: red;">حاول ثانيتا هناك ضغط على شراء هذه الفئة</h>
                                            @else
                                                <div id="liveAlertPlaceholder"></div>
                                                <a href="{{ route('addtocoderecord.create', $code->id) }}"
                                                    class="btn btn-primary purchase-btn" id="purchase-btn"
                                                    onclick="handlePurchase(event)">شراء الكود</a>
                                            @break
                                    @endif

                                @endif

                                {{-- @can('is-admin')
                            <!-- زر الحذف -->
                            <form action="{{ route('codes.destroy', $code->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">حذف الكود</button>
                            </form>
                            @endcan --}}
                            </div>
                        </div>
                        {{-- انهاء الحلقة بمجرد عرض كود واحد --}}
                    @break

                </div>
            @endif
        @endforeach
    </div>
    {{-- إذا لم يكن هناك أكواد --}}
    @if (count($shuffledCodes) === 0)
        <div class="col-lg-12">
            <h5 style="color: red">لا يوجد أكواد من فئة {{ $category->type }} حاليًا، ستتوفر قريبًا بإذن الله.</h5>
        </div>
    @endif
</section>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">سجل الاكواد</h5>





                    <!-- Table with stripped rows -->
                    <table class="table " id="catTable">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th> الكود </th>
                                <th class="text-center align-middle" data-type="date" data-format="YYYY/DD/MM">تاريخ
                                    الاظافة</th>
                                <th class="text-center align-middle" data-type="date" data-format="YYYY/DD/MM">تاريخ
                                    الشراء</th>
                                <th>حالة الكود</th>
                                <th>المشتري</th>
                                <th>الاحداث </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($codes as $code)
                                <tr class="text-center align-middle">
                                    <td>{{ $code->id }}</td>
                                    <td>{{ $code->code }}</td>
                                    <td class="text-center align-middle">
                                        {{ date_format($code->created_at, 'H:i / Y-m-d') }}</td>

                                    <td class="text-center align-middle">
                                        @if ($code->purchaseDate())
                                            {{ date_format($code->purchaseDate(), 'H:i / Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($code->client())
                                            مباع
                                        @else
                                            متوفر
                                        @endif
                                    </td>
                                    <td>
                                        @if ($code->client())
                                            {{ $code->client()->name }}@else-
                                        @endif
                                    </td>
                                    <td>
                                        <form id="CodeDelete_{{ $code->id }}" class="d-inline"
                                            action="{{ route('codes.destroy.post') }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <input type="hidden" name="id" value="{{ $code->id }}">
                                            <a href="javascript:void(0);" disabled
                                                onclick="submitForm({{ $code->id }},'{{ preg_replace('/\r|\n/', '', $code->code) }}',this)">
                                                <img src="{{ asset('img/delete.svg') }}" alt="delet image">
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <!-- End Table with stripped rows -->
                </div>
                <div class="d-flex">

                    {{ $codes->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
    aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">تأكيد الشراء</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>اهدأ يا نقرة وحدة بتكفي</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        @if (session('code_purchased'))
            <span>اضغط <a href="{{ route('code_management.index') }}" onclick="refreshPage()">هنا</a> لعرض
                الأكواد.</span>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

</main>

<script>
    // عرض الرسالة بعد تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        var successAlert = document.getElementById('success-alert');
        if (successAlert) {
            successAlert.style.display = 'block';
        }
    });

    var isPurchaseClicked = false;

    function handlePurchase(event) {
        if (!isPurchaseClicked) {
            isPurchaseClicked = true;

            // اكمال عملية الشراء
            // يمكنك هنا إضافة الكود الخاص بإكمال عملية الشراء

        } else {
            // منع النقر المتعدد
            event.preventDefault();

            // عرض الرسالة المنبثقة
            $('#confirmationModal').modal('show');
        }
    }

    function refreshPage() {
        location.reload();
    }

    const alertPlaceholder = document.getElementById('liveAlertPlaceholder')
    const appendAlert = (message, type) => {
        const wrapper = document.createElement('div')
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible" role="alert">`, `   <div>${message}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('')

        alertPlaceholder.append(wrapper)
    }

    const alertTrigger = document.getElementById('purchase-btn')
    if (alertTrigger) {
        alertTrigger.addEventListener('click', () => {
            appendAlert('سيتم شراء الكود جاري المعالجة', 'success')
        })
    }
</script>

@endsection


@push('endjs')
<script src="{{ asset('assets/vendor/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sweetalert/sweetalerts.min.js') }}"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#catTable", {
        paging: false

    });
    var table = document.querySelector('#catTable');
    var cells = table.querySelectorAll('td, th', 'tr');
    cells.forEach(function(cell) {
        cell.classList.add('text-center', 'align-middle');
    });

    function submitForm(id, key, button) {
        Swal.fire({
            title: `هل تريد حقاً حذف الكود  ${key}؟`,
            html: `<br> <strong><span class="text-danger">لا</span> يمكن التراجع عن هذا الإجراء! وفي حال تم شراء الكود سيختفي من عند العميل</strong>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('CodeDelete_' + id).submit();
            }
        });
    }
</script>

<script></script>
@endpush
