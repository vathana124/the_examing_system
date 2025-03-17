<?php

namespace App\Filament\Resources\UserRegistrationResource\Pages;

use App\Filament\Resources\UserRegistrationResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRegistration extends CreateRecord
{
    protected static string $resource = UserRegistrationResource::class;

    protected static bool $canCreateAnother = false;

    public $role_id;

    public function form(Form $form): Form
    {
        return $form
                ->schema([
                    Fieldset::make('User Registration')
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    Select::make('role_id')
                                        ->label('Role')
                                        ->live(debounce:500)
                                        ->options(function(){
                                            return getRoles();
                                        })
                                        ->required()
                                    ]),
                            Fieldset::make('Input Information')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('full_name')
                                                ->label('Full Name')
                                                ->required()
                                                ->placeholder('Ex. Pheng Vathana')
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(55),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email()
                                                ->required()
                                                ->placeholder('Ex. VathanaPheng@gmail.com')
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(55),
                                            TextInput::make('password')
                                                ->label('password')
                                                ->password()
                                                ->revealable()
                                                ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                                                ->dehydrated(fn ($state) => filled($state))
                                                ->nullable(),
                                            TextInput::make('phone_number')
                                                ->label('Phone Number')
                                                ->placeholder('Ex. 012344455555')
                                                ->rule(['regex:/^\+?[0-9]*$/'])
                                                ->unique(ignoreRecord: true)
                                                ->required()
                                                ->maxLength(55),
                                            DatePicker::make('birth')
                                                ->live(debounce:500)
                                                ->label('Birth')
                                                ->native(false)
                                                ->displayFormat('d F  Y')
                                                ->required(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return false;
                                                        }
                                                        else{
                                                            return true;
                                                        }
                                                    }
                                                    else{
                                                        return true;
                                                    }
                                                })
                                                ->hidden(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return true;
                                                        }
                                                        else{
                                                            return false;
                                                        }
                                                    }
                                                    else{
                                                        return false;
                                                    }
                                                }),
                                            TextInput::make('year')
                                                ->live(debounce:500)
                                                ->placeholder('Ex. 1')
                                                ->label('Year')
                                                ->required(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return false;
                                                        }
                                                        else{
                                                            return true;
                                                        }
                                                    }
                                                    else{
                                                        return true;
                                                    }
                                                })
                                                ->hidden(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return true;
                                                        }
                                                        else{
                                                            return false;
                                                        }
                                                    }
                                                    else{
                                                        return false;
                                                    }
                                                }),
                                            TextInput::make('class')
                                                ->live(debounce:500)
                                                ->label('Class')
                                                ->placeholder('Ex. E4')
                                                ->required(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return false;
                                                        }
                                                        else{
                                                            return true;
                                                        }
                                                    }
                                                    else{
                                                        return true;
                                                    }
                                                })
                                                ->hidden(function($get){
                                                    $role_id = (int) $get('role_id');
                                                    if($role_id){
                                                        $role = getRole($role_id);
                                                        if(in_array($role?->name, [config('access.role.admin'), config('access.role.teacher')])){
                                                            return true;
                                                        }
                                                        else{
                                                            return false;
                                                        }
                                                    }
                                                    else{
                                                        return false;
                                                    }
                                                }),
                                        ])
                                ]) 
                                ->live(debounce:500)
                                ->hidden(function($get){
                                    $role_id = (int) $get('role_id');
                                    if($role_id){
                                        return false;
                                    }
                                    return true;
                                })       
                        ])
                        ->columns(1)
                ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $this->role_id = (int) $data['role_id'];
        $user = auth()->user();

        unset($data['role_id']);

        $data['name'] = $data['full_name'];
        $data['created_by'] = $user?->id;
        $data['updated_by'] = $user?->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user= $this->getRecord();
        $role_id = $this->role_id;
        // assign role to user
        $user->roles()->sync($role_id);
        $user->save();
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.user-registrations.index');
    }
}
