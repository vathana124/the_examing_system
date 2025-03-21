<?php

namespace App\Livewire;

use App\Models\Role;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class CustomRegister extends Register
{
    protected static string $view = 'livewire.custom-register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getClassFormComponent(),
                        $this->getYearFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('Student Name'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getClassFormComponent(): Component
    {
        return TextInput::make('class')
            ->label(__('Student Class'))
            ->required()
            ->maxLength(20);
    }

    protected function getYearFormComponent(): Component
    {
        return TextInput::make('study_year')
            ->label(__('Study Year'))
            ->required()
            ->maxLength(20);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('Student Email'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            // ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    public function getTitle(): string | Htmlable
    {
        return __('Student Registration');
        // return false;
    }

    public function getHeading(): string | Htmlable
    {
        return __('Registration Form');
        // return false;
    }

    // public function getSubheading(): string | Htmlable | null
    // {
    //     return false;
    // }

    // public function hasLogo(): bool
    // {
    //     return false;
    // }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(__('Already Registration'))
            ->url(filament()->getLoginUrl());
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            // to add user role by default is student

            // Assign admin role to the user
            $role = Role::firstOrNew(['name' => config('access.role.student')]);
            $user->roles()->sync($role->id);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    // protected function afterRegister(): void
    // {
    // //    $record = $this->getRecord();
    //    dd($this->userModel);
    // }
}
