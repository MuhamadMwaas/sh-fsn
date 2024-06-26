<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::create([
            'type' => 'M_mode',
            'key' => 'M_mode',
            'value' => false
        ]);
        Settings::create([
            'type' => 'M_mode',
            'key' => 'M_mode_message',
            'value' => "يرجى المحاولة لاحقاً الموقع في وضع الصيانة"
        ]);
    }
}
