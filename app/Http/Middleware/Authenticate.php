<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class Authenticate extends BaseAuthenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentPanel();

        // dd($user);

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // Check if Email OTP disabled
        if(!config('auth.enable_email_otp')) {
            return $next($request);
        }

        //get user
        $user = auth()->user();

        //if user fail to verify otp
        if($user?->verify_otp_false){
            $user->verify_otp_false = false;
            $user->save();
            return redirect()->route('admin.auth.otp');
        }
        
        // Check if the user has verified 2FA
        if($user->token_2fa_expiry > Carbon::now()){
            return $next($request);
        }

        // expired date
        if($user->token_2fa_expiry < Carbon::now() && !empty($user->token_2fa_expiry)){
            // logut user
            $user->token_2fa_expiry = null;
            $user->token_2fa = null;
            $user->save();
            Auth::logout();
            return redirect()->route('filament.admin.auth.login');
        }

        // if($user->email_otp){
        //     $this->sendOtp($user);
        // }

        return redirect()->route('admin.auth.otp');
    }

    private function sendOtp($user)
    {
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $user->token_2fa = $otp;
        $user->save();

        // to check if process down
        try {
            $executed = RateLimiter::attempt(
                'send-to:'.$user->email_otp,
                $perMinute = 5,
                function() use ($user) {
                    // Send the 6-digit code via email_otp, SMS, etc.
                    Mail::raw("Your OTP is: $user->token_2fa", function ($message) use ($user) {
                        $message->to($user->email_otp)->subject('Your Login OTP');
                    });
                }
            );
    
            if (! $executed) {
                Notification::make()
                    ->title('លើសដែនកំណត់អត្រា') // "Rate Limit Exceeded" in Khmer
                    ->body('ការព្យាយាមច្រើនដងពេក។ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។') // "Too many attempts. Please try again later." in Khmer
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title('OTP ត្រូវបានផ្ញើ') // "OTP Sent" in Khmer
                    ->body('កូដ OTP ៦ ខ្ទង់ត្រូវបានផ្ញើទៅកាន់អ៊ីមែលរបស់អ្នក។') // "A 6-digit OTP has been sent to your email." in Khmer
                    ->success()
                    ->send();
            }
        } catch (\Throwable $th) {
            Notification::make()
                ->title('កំហុសក្នុងការដំណើរការ') // "Rate Limit Exceeded" in Khmer
                ->body('ការព្យាយាមច្រើនដងពេក។ លេខកូដ OTP មិនអាចផ្ញើបានទេ។') // "Too many attempts. Please try again later." in Khmer
                ->danger()
                ->send();
        }
    }

}