<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }
    protected $casts = [
        'value' => 'array', // تحويل value إلى مصفوفة
    ];

    public static function M_mode()
    {
        return self::where('key', 'M_mode')->first();
    }
    public static function M_mode_message()
    {
        return self::where('key', 'M_mode_message')->first();
    }
}
