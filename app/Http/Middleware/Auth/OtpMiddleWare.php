<?php

namespace App\Http\Middleware\Auth;

use Carbon\Carbon;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class OtpMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user() ?? null;

        if($user){
            // Check if Email OTP disabled
            if(!config('auth.enable_email_otp')) {
                return redirect()->route('filament.admin.auth.login');
            }

            // Check if the user has verified 2FA
            if($user->token_2fa_expiry > Carbon::now()){
                return redirect()->route('filament.admin.pages.dashboard');
            }
            
            return $next($request);
        }
        return redirect()->route('filament.admin.auth.login');
    }

}
