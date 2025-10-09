<x-filament-panels::page.simple>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            text-align: center;
        }

        .logo {
            padding: 15px 15px 5px;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        .logo img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .title {
            font-size: 26px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 2px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .subtitle p {
            font-size: 14px;
        }

        .login-form {
            padding: 20px;
        }

        .login-form .form-group {
            text-align: left;
            margin-bottom: 5px;
        }

        .login-form label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        .login-form input:focus {
            border-color: rgba(217, 119, 6, 1);
            box-shadow: 0 0 4px rgba(217, 119, 6, 1);
        }

        .login-button {
            background-color: rgba(217, 119, 6, 1);
            border-color: rgba(217, 119, 6, 1);
            color: #ffffff;
            font-weight: bold;
            padding: 14px 10px; 
            width: 100%;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .login-button:hover {
            opacity: 0.8;
        }

        .footer {
            margin-top: 10px;
        }
        .footer p{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 10px;
            font-size: 12px;
        }

        .footer-text {
            font-weight: bold;
        }
        .text-red-500 {
            color: red !important;
        }
        .margin-top {
            margin-top: 5px;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        .text-custom-blue{
            color: rgba(217, 119, 6, 1);
        }

        /* Existing styles */
        .inline-block.align-baseline.font-bold.text-sm.text-custom-blue {
            color: rgba(217, 119, 6, 1); /* Default color */
            text-decoration: none; /* Remove underline */
            transition: color 0.3s ease, opacity 0.3s ease; /* Smooth transition */
        }

        /* Hover state */
        .inline-block.align-baseline.font-bold.text-sm.text-custom-blue:hover {
            color: rgba(217, 119, 6, 1); /* Darker shade of blue */
            opacity: 0.8; /* Slight fade effect */
        }

        /* Active state (when clicked) */
        .inline-block.align-baseline.font-bold.text-sm.text-custom-blue:active {
            color: rgba(217, 119, 6, 1); /* Even darker shade of blue */
            opacity: 0.9; /* Slightly less fade than hover */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                padding: 15px;
            }
    
            .title {
                font-size: 22px;
            }
    
            .subtitle {
                font-size: 18px;
            }

            .subtitle p {
                font-size: 12px;
            }
        
            .login-form input {
                padding: 8px;
                font-size: 13px;
            }
    
            .login-button {
                font-size: 13px;
                padding: 8px;
            }
    
            .footer {
                font-size: 11px;
            }
        }
    
        @media (max-width: 480px) {
            .logo img {
                width: 80px;
            }
    
            .title {
                font-size: 20px;
            }
    
            .subtitle {
                font-size: 16px;
            }
            .subtitle p {
                font-size: 10px;
            }
    
            .login-form input {
                font-size: 12px;
                padding: 6px;
            }
    
            .login-button {
                font-size: 12px;
                padding: 6px;
            }
    
            .footer {
                font-size: 10px;
            }
        }
        @media (max-width: 300px) {
            .logo img {
                width: 80px;
            }
    
            .title {
                font-size: 18px;
            }
    
            .subtitle {
                font-size: 14px;
            }

            .subtitle p {
                font-size: 8px;
            }
    
            .login-form input {
                font-size: 10px;
                padding: 4px;
            }
    
            .login-button {
                font-size: 10px;
                padding: 4px;
            }
    
            .footer {
                font-size: 8px;
            }
        }
    </style>

    <div class="login-container">
        <div class="logo">
            <img src="{{ asset('assets/images/logo-v-2.png') }}" alt="Logo">
            <div class="title">
                <h1>{{ config('custom_translations.otp.title') }}</h1>
            </div>
        </div>
        <hr>
        <div class="subtitle">
            <h2>{{ config('custom_translations.otp.sub_heading') }}</h2>
            <br>
            @if ($sub_header)
                <p> {{ config('custom_translations.otp.email_title') }} </p>
                <p> {{ config('custom_translations.otp.email_sub_title') }} : {{ $sub_header }}</p>
            @endif
        </div>

        <!-- Display Validation Errors -->

        <!-- Login Form -->
        <x-filament-panels::form id="otp-form" class="login-form" wire:submit.prevent="authenticate">
            @if (!$user?->email_otp)

                <div class="form-group">
                    <input 
                        class="fi-input block w-full border transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                        id="data.email" 
                        type="email"
                        placeholder="អាស័យ​ដ្ឋាន​អ៊ី​ម៉េ​ល" 
                        wire:model="data.email"
                        required
                        
                    >
                    @error('data.email') 
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
            @endif
            <div class="form-group">
                <div class="flex justify-center gap-2 mb-6">
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.0"
                           data-otp-input>
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.1"
                           data-otp-input>
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.2"
                           data-otp-input>
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.3"
                           data-otp-input>
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.4"
                           data-otp-input>
                    <input class="w-12 h-12 text-center border rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                           type="text" 
                           maxlength="1" 
                           pattern="[0-9]" 
                           inputmode="numeric" 
                           autocomplete="one-time-code" 
                           required
                           wire:model="data.otp.5"
                           data-otp-input>
                </div>
                @error('data.otp') 
                    <div class="error-message">{{ $message }}</div>
                @enderror
                @error('data.otp.*') 
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-sm" id="resend_con">
                    <div x-data="timer">
                        <!-- Resend OTP Link (hidden when timer is active) -->

                        @if (!$is_send_otp)
                            <a id="send" x-show="remainingSeconds <= 0" class="inline-block align-baseline font-bold text-sm text-custom-blue hover:text-custom-blue-hover ml-4" href="#" x-on:click="startTimer($wire.resend_time)" wire:click.prevent="IsSendOtp">
                                {{ config('custom_translations.otp.otp_label') }}
                            </a>
                        @endif

                        @if ($is_send_otp)
                            <div class="" id="resend-container">
                                <span
                                    x-show="remainingSeconds <= 0"
                                >
                                {{ config('custom_translations.otp.resend_title') }}
                                </span>
                                <a
                                    id="is_resend"
                                    href="#"
                                    x-show="remainingSeconds <= 0"
                                    x-on:click="startTimer($wire.resend_time)"
                                    wire:click.prevent="IsResendOtp"
                                    class="inline-block align-baseline font-bold text-sm text-custom-blue hover:text-custom-blue-hover ml-4"
                                >
                                {{ config('custom_translations.otp.otp_label') }}
                                </a>
                            </div>
                        @endif
            
                        <!-- Timer (visible when timer is active) -->
                        <span
                            x-show="remainingSeconds > 0"
                        >
                            {{ config('custom_translations.otp.time_label') }}
                        </span>
                        <br>
                        <span
                            x-show="remainingSeconds > 0"
                            x-text="formatTime()"
                            x-init="startTimer($wire.resend_time)"
                            class="ml-2 font-bold"
                            x-bind:class="{
                                'text-red-500': remainingSeconds < 30,
                                'text-yellow-500': remainingSeconds >= 30 && remainingSeconds < 60,
                                'text-green-500': remainingSeconds >= 60
                            }"
                            wire:ignore
                        >
                            <!-- Timer will be displayed here -->
                        </span>
                    </div>
            </div>

            <button id="submit" type="button" wire:click.prevent="authenticate" class="login-button">{{ config('custom_translations.otp.submit') }}</button>

        </x-filament-panels::form>

        <footer class="footer">
            {{-- <p>
                <span>&copy; រក្សាសិទ្ធិដោយ </span> 
                <span class="footer-text"> {{ config('custom_translations.otp.copy_right') }}</span>
            </p> --}}
        </footer>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const otpInputs = document.querySelectorAll('[data-otp-input]');

                otpInputs.forEach((input, index) => {
                    input.addEventListener('input', (e) => {
                        if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    });

                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Backspace' && index > 0 && !e.target.value) {
                            otpInputs[index - 1].focus();
                        }
                        // Submit OTP on Enter
                        if (e.key === 'Enter') {
                            document.querySelector('.login-button').click();
                        }
                    });

                    // Handle paste event
                    input.addEventListener('paste', (e) => {
                        e.preventDefault();
                        const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                        if (/^\d{6}$/.test(pastedData)) { // Ensure only 6 digits
                            otpInputs.forEach((input, i) => {
                                input.value = pastedData[i] || '';

                                // dispatchEvent to let livewire know change of inputs
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                            });
                            otpInputs[otpInputs.length - 1].focus();
                        }
                    });
                });

                const form = document.getElementById('otp-form');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();

                    let otpCode = '';
                    otpInputs.forEach(input => {
                        if (!input.value) {
                            input.setCustomValidity('Please enter the OTP code');
                            input.reportValidity();
                            return;
                        }
                        otpCode += input.value;
                    });

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'otp';
                    hiddenInput.value = otpCode;
                    form.appendChild(hiddenInput);

                    form.submit();
                });

                    var send = document.getElementById('is_resend');
                    if(send){
                        send.addEventListener('click', function (e) {
                            e.preventDefault();
                            document.getElementById('resend-container').hidden = true;
                        });
                    }

                    var send = document.getElementById('send');
                    if(send){
                        send.addEventListener('click', function (e) {
                            e.preventDefault();
                            this.classList.add("hidden");
                        });
                    }

                document.addEventListener('keydown', (e) => {
                    // Submit OTP on Enter
                    if (e.key === 'Enter') {
                        document.querySelector('.login-button').click();
                    }
                });
            });

        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('timer', () => ({
                    remainingSeconds: 0, // Initial remaining seconds (0 means timer is inactive)
                    timer: null, // Timer interval reference

                    // Initialize the timer
                    init() {
                        // Optional: Reset timer when the component is initialized
                        this.remainingSeconds = 0;
                    },

                    // Start the timer
                    startTimer(resend_time) {
                        this.remainingSeconds = resend_time; // Reset to 2 minutes
                        if (this.timer) {
                            clearInterval(this.timer); // Clear any existing timer
                        }

                        this.timer = setInterval(() => {
                            if (this.remainingSeconds > 0) {
                                this.remainingSeconds--;
                            } else {
                                this.stopTimer();
                            }
                        }, 1000);
                    },

                    // Stop the timer
                    stopTimer() {
                        if (this.timer) {
                            clearInterval(this.timer);
                            this.timer = null;
                        }
                    },

                    // Format the remaining time as MM:SS
                    formatTime() {
                        const minutes = Math.floor(this.remainingSeconds / 60).toString().padStart(2, '0');
                        const seconds = Math.floor(this.remainingSeconds % 60).toString().padStart(2, '0');
                        return `${minutes}:${seconds}`;
                    }
                }));
            });
        </script>
    @endpush
</x-filament-panels::page.simple>