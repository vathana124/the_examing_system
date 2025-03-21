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
            font-size: 0.875rem;
            margin-top: 20px;
            margin-bottom: 2px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    
        .login-form {
            padding: 20px;
        }
    
        .login-form .form-group {
            text-align: left;
            margin-bottom: 15px;
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
    
        .footer p {
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
            <img src="{{ asset('assets/images/exam-results.png')}}" alt="Logo">
            <div class="title">
                <h1>{{ config('custom_translations.login.title') }}</h1>
            </div>
        </div>
        <hr>
        <div class="subtitle">
            <h2>{{ config('custom_translations.login.sub_heading') }} ? {{ $this->registerAction() }}</h2>
        </div>

        <!-- Display Validation Errors -->

        <!-- Login Form -->
        <x-filament-panels::form id="form" class="login-form" wire:submit.prevent="authenticate">
            <div class="form-group">
                <input 
                    class="fi-input block w-full border transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                    id="data.email" 
                    type="email" 
                    placeholder="Email" 
                    wire:model="data.email"
                    required
                >
                @if ($errors->any())
                    <div class="error-message" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @error('data.email') 
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <input 
                    class="fi-input block w-full border transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 sm:text-sm bg-white/0 ps-3 pe-3" 
                    id="data.password" 
                    type="password" 
                    placeholder="Password" 
                    wire:model="data.password"
                    required
                >
                @error('data.password') 
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="login-button">Login</button>
        </x-filament-panels::form>

        <footer class="footer">
            {{-- <p>
                <span>&copy; រក្សាសិទ្ធិដោយ </span> 
                <span class="footer-text"> {{ config('custom_translations.login.copy_right') }}</span>
            </p> --}}
        </footer>
    </div>
</x-filament-panels::page.simple>