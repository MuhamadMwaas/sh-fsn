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
}
