<?php

namespace App\Enum;

enum TransferType: int
{
    case PaidCharge = 1;
    case DebtCharge = 2;
    case DebtRepayment = 3;
    case buyCode = 4;
    case buyInternationalsim = 5;
    case ChangeBalance = 6;

    public function description(): string
    {
        return match ($this) {
            TransferType::PaidCharge => 'شحن مدفوع',
            TransferType::DebtCharge => 'شحن بالدين',
            TransferType::DebtRepayment => 'تسديد دين',
            TransferType::buyCode => 'شراء كود',
            TransferType::buyInternationalsim => 'شراء تفعيل خط دولي',
            TransferType::ChangeBalance => 'تعديل رصيد من قبل المدير',
        };
    }

    public function class(): string
    {
        return match ($this) {
            TransferType::PaidCharge => 'badge text-bg-success',
            TransferType::DebtCharge => 'badge rounded-pill text-bg-danger',
            TransferType::DebtRepayment => 'badge rounded-pill text-bg-info',
            TransferType::buyCode => 'badge rounded-pill text-bg-primary',
            TransferType::buyInternationalsim => 'badge rounded-pill text-bg-light',
            TransferType::ChangeBalance => 'badge rounded-pill text-bg-warning',
        };
    }
}
