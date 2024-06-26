<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $MaintenancFlage = Settings::M_mode()->value;
        if (Auth::check() && $MaintenancFlage == "true") {
            if (!Gate::allows('is-admin')) {
                Auth::logout();
                $massage = isset(Settings::M_mode_message()->value) ? Settings::M_mode_message()->value : 'الموقع في وضع الصيانة حاول لاحقاً';
                return redirect()->route('login')->withErrors(['email' => $massage]);
            }
        }
        return $next($request);
    }
}
