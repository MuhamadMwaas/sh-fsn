<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class Setting extends Controller
{


    public function Update(Request $request)
    {


        $M_mode_status = Settings::M_mode();
        $M_mode_message = Settings::M_mode_message();
        if ($request->status) {
            $M_mode_status->value = true;
        } else {
            $M_mode_status->value = false;
        }
        $M_mode_status->save();

        $M_mode_message->value = $request->massage;
        $M_mode_message->save();

        return redirect()->route('profiles')->with('success', 'تم تعديل بيانات وضع الصيانة');
    }
}
