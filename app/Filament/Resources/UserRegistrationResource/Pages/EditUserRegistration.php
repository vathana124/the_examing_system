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
use Filament\Resources\Pages\EditRecord;

class EditUserRegistration extends EditRecord
{
    protected static string $resource = UserRegistrationResource::class;
    
    public $role_id;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

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
                                        ->options(function($record){
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
                                                ->maxLength(55),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email()
                                                ->required()
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
                                                ->rule(['regex:/^\+?[0-9]*$/'])
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getRecord();
        $role_id = $user->roles[0]?->id ?? null;
        if($role_id){
            $data['role_id'] = $role_id;
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->role_id = (int) $data['role_id'];

        unset($data['role_id']);

        $data['name'] = $data['full_name'];

        return $data;
    }

    protected function afterSave(): void
    {
        $user= $this->getRecord();
        $role_id = $this->role_id;
        // assign role to user
        $user->roles()->sync($role_id);
        $user->save();
    }
}
