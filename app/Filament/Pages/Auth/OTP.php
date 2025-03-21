<?php

namespace App\Filament\Pages\Auth;

use Carbon\Carbon;
use Closure;
use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SimplePage;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class OTP extends BaseAuth
{
    protected static string $view = 'filament-panels::pages.auth.otp';

    public $user;
    public $is_send_otp;

    public $resend_time = 0;

    public $sub_header = null;

    protected $listeners = [
        're-send-otp' => 'Send',
        'is_click_send' => 'sendButton',
        'is_click_re_send' => 'reSendButton',
        'updateView' => '$refresh',
    ];

    // Custom validation messages
    public $messages = [
                'data.email.required' => 'The email address is required.',
                'data.email.email' => 'Please enter a valid email address.',
                'data.otp.required' => 'The OTP is required.',
                'data.otp.array' => 'The OTP must be an array.',
                'data.otp.size' => 'The OTP must be exactly 6 digits.',
                'data.otp.*.required' => 'Each OTP digit is required.',
                'data.otp.*.integer' => 'Each OTP digit must be a number.',
                'data.otp.*.digits' => 'Each OTP digit must be a single digit (0-9).',
            ];
    

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->form->fill();

        // $this->resend_time = ((int)config('auth.resend_time')) * 10;

        if($this->user?->email_otp){
            $this->is_send_otp = true;
            $maskedEmail = $this->maskEmail($this->user?->email_otp);
            $this->sub_header = ($maskedEmail);
            if($this->user?->otp_resend_duration){
                $minsleft = Carbon::now()->diffInMinutes($this->user?->otp_resend_duration, false);

                if($minsleft > 0){
                    $this->resend_time = $minsleft * 60;
                }
                else{
                    $this->resend_time = 0 * 60;
                }
            }
        }
        else{
            if($this->user?->otp_input_email){
                $this->data['email'] = $this->user?->otp_input_email;
                if($this->user?->otp_resend_duration){
                    $minsleft = Carbon::now()->diffInMinutes($this->user?->otp_resend_duration, false);
    
                    if($minsleft > 0){
                        $this->resend_time = $minsleft * 60;
                    }
                    else{
                        $this->resend_time = 0 * 60;
                    }
                }
            }
        }
    }

    protected function maskEmail(string $email): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $email);
            $username = $parts[0];
            $domain = $parts[1];

            // Mask the username part (e.g., "xx*******")
            $maskedUsername = substr($username, 0, 6) . str_repeat('*', max(0, strlen($username) - 2));

            return $maskedUsername . '@' . $domain;
        }

        return $email; // Return the original email if it's invalid
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        // custom data validation
        $data = $this->validateData();

        if(!$data['email_otp']){
            return app(LoginResponse::class);
        }
        elseif(!$data['otp_code']){
            return app(LoginResponse::class);
        }

        // to check otp code is correct or not
        if($this->user?->token_2fa == (int)$data['otp_code']){
            // Mark 2FA as verified
            $time = (int) config('auth.expired_date');
            $this->user->token_2fa_expiry = Carbon::now()->addMinutes($time);
            $this->user->save();

            // Update user OTP status
            if(!$this->user->email_otp && $data['email_otp']){
                $this->user->email_otp = $data['email_otp'];
                $this->user->save();
            }
        }
        else{
            Notification::make()
                ->title('Login Failed') // "Login Fail" in English
                ->body('Incorrect OTP code') // "Wrong OTP code" in English
                ->danger()
                ->send();
            $this->user->verify_otp_false = true;
            $this->user->save();
        }
        if (
            ($this->user instanceof FilamentUser) &&
            (!$this->user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    
    // verify otp function
    public function validateData(){

        // validation data
        $this->validationData();

        $email_otp = $this->data['email'] ?? null;

        if(!$email_otp){
            $email_otp = $this->user?->email_otp;
        }

        // make otp array to a code

        $otp_code = implode('', $this->data['otp']);

        return [
            'email_otp' => $email_otp,
            'otp_code' => $otp_code
        ];
    }

    // validation data
    public function validationData(){

        if($this->user?->email_otp){
            // Validate the input data with custom messages
            $this->validate([
                'data.otp' => 'required|array|size:6',
                'data.otp.*' => 'required|integer|digits:1',
            ], $this->messages);
        }else{
            // Validate the input data with custom messages
            $this->validate([
                'data.email' => 'required|email',
                'data.otp' => 'required|array|size:6',
                'data.otp.*' => 'required|integer|digits:1',
            ], $this->messages);
        }
    }

    //handle for once send otp code
    public function IsSendOtp(){

        $this->dispatch('is_click_send');

        $this->validate([
            'data.email' => 'required|email',
        ], $this->messages);

        $email_otp = $this->data['email'];

        $this->user->otp_input_email = $email_otp;

        $time = (int) config('auth.resend_time');

        $this->user->otp_resend_duration = Carbon::now()->addMinutes($time);
        $this->user->token_2fa = null;
        $this->user->save();

        $minsleft = Carbon::now()->diffInMinutes($this->user?->otp_resend_duration, false);

        if($minsleft > 0){
            $this->resend_time = $minsleft * 60;
        }
        else{
            $this->resend_time = 0 * 60;
        }

        // $this->dispatch('updateView');

        // send otp code
        $this->sendOtp($this->user, $email_otp);

        $this->is_send_otp = true;
    }

    // handle mutiple send otp code
    public function IsResendOtp(){

        $this->dispatch('is_click_re_send');

        $email_otp = null;
        if($this->user?->email_otp){
            $email_otp = $this->user?->email_otp;
        }
        else{
            $this->validate([
                'data.email' => 'required|email',
            ], $this->messages);
    
            $email_otp = $this->data['email'];
        }

        $time = (int) config('auth.resend_time');

        $this->user->otp_resend_duration = Carbon::now()->addMinutes($time);
        $this->user->token_2fa = null;
        $this->user->save();
        $minsleft = Carbon::now()->diffInMinutes($this->user?->otp_resend_duration, false);

        if($minsleft > 0){
            $this->resend_time = $minsleft * 60;
        }
        else{
            $this->resend_time = 0 * 60;
        }

        // $this->dispatch('updateView');
        
        // send otp code
        $this->sendOtp($this->user, $email_otp);

        $this->is_send_otp = true;
    }

    public function getHeading(): string | Htmlable
    {
        return false;
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function sendOtp($user, $email_otp)
    {
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $user->token_2fa = $otp;
        $user->save();
    
        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }
    
        // Check if it's the first attempt
        $isFirstAttempt = session('is_first_attempt', true);
    
        try {
            if ($isFirstAttempt) {
                // Bypass rate limiter for the first attempt
                $retries = 2;
                while ($retries > 0) {
                    try {
                        Mail::raw("Your OTP is: $user->token_2fa", function ($message) use ($email_otp) {
                            $message->to($email_otp)->subject('Your Login OTP');
                        });
                        break; // Exit the loop if email is sent successfully
                    } catch (\Throwable $e) {
                        $retries--;
                        if ($retries === 0) {
                            Notification::make()
                                ->title('Rate Limit Exceeded') // "Rate Limit Exceeded" in English
                                ->body('Too many attempts. Please try again later.') // "Too many attempts. Please try again later." in English
                                ->danger()
                                ->send();
                            $this->dispatch('re-send-otp');
                            return;
                        }
                        sleep(1); // Wait for 1 second before retrying
                    }
                }
    
                // Mark first attempt as completed
                session(['is_first_attempt' => false]);
            } else {
                // Apply rate limiter for subsequent attempts
                $executed = RateLimiter::attempt(
                    'send-to:' . $email_otp,
                    $perMinute = 5,
                    function() use ($user, $email_otp) {
                        // Retry sending email up to 3 times
                        $retries = 3;
                        while ($retries > 0) {
                            try {
                                Mail::raw("Your OTP is: $user->token_2fa", function ($message) use ($email_otp) {
                                    $message->to($email_otp)->subject('Your Login OTP');
                                });
                                break; // Exit the loop if email is sent successfully
                            } catch (\Throwable $e) {
                                $retries--;
                                if ($retries === 0) {
                                    throw $e; // Re-throw the exception if all retries fail
                                }
                                sleep(1); // Wait for 1 second before retrying
                            }
                        }
                    }
                );
    
                if (! $executed) {
                    Notification::make()
                        ->title('Rate Limit Exceeded') // "Rate Limit Exceeded" in English
                        ->body('Too many attempts. Please try again later.') // "Too many attempts. Please try again later." in English
                        ->danger()
                        ->send();
                    $this->dispatch('re-send-otp');
                    return;
                }
            }
    
            // Notify success
            Notification::make()
                ->title('OTP Sent') // "OTP Sent" in English
                ->body('A 6-digit OTP has been sent to your email.') // "A 6-digit OTP has been sent to your email." in English
                ->success()
                ->send();
            $this->dispatch('re-send-otp');
    
        } catch (\Throwable $th) {
    
            Notification::make()
                ->title('Processing Error') // "Processing Error" in English
                ->body('OTP code could not be sent. Please try again.') // "OTP code could not be sent. Please try again." in English
                ->danger()
                ->send();
            $this->dispatch('re-send-otp');
        }
    }
}