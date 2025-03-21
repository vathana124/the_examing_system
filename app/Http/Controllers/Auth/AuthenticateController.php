<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    public function logout() {
        
        // clear data when logout to required new otp code

        if(config('auth.enable_email_otp')) {
            if(config('auth.logout_remove_otp')){
                $user = auth()->user();
                $user->token_2fa_expiry = null;
                $user->token_2fa = null;
                $user->otp_resend_duration = null;
                $user->save(); 
            }
        }

        // logout user 
        Auth::logout();

        // to login page
        $redirectUrl = route('filament.admin.auth.login');
        return redirect($redirectUrl);
    }
}
