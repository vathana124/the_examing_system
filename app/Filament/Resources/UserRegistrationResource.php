<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRegistrationResource\Pages;
use App\Filament\Resources\UserRegistrationResource\RelationManagers;
use App\Models\Exam;
use App\Models\User;
use App\Models\UserRegistration;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class UserRegistrationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'User Registration';

    protected static ?string $modelLabel = 'User Registration';

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string
    {
        return asset('icons/flaticon/user-2.png');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d F Y'),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d F Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('allow_to_exam')
                        ->label('Allow To Exam')
                        ->color('info')
                        ->icon('heroicon-o-academic-cap')
                        ->requiresConfirmation()
                        ->modalHeading('Do you want to allow to exam ?') // Confirmation modal title
                        ->disabled(function($record){
                            $user = auth()->user();
                            if($user->isSuperAdmin()){
                                return true;
                            }
                            else{
                                if($record->isStudent()){
                                    $teachers = json_decode($record?->teachers,true);
                                    if($teachers){
                                        if(in_array($user?->id, $teachers)){
                                            return true;
                                        }
                                        return false;
                                    }
                                    return false;
                                }
                                return true;
                            }
                        })
                        ->hidden(function($record){
                            $user = auth()->user();
                            if($user->isSuperAdmin()){
                                return true;
                            }
                            else{
                                if($record->isStudent()){
                                    $teachers = json_decode($record?->teachers,true);
                                    if($teachers){
                                        if(in_array($user?->id, $teachers)){
                                            return true;
                                        }
                                        return false;
                                    }
                                    return false;
                                }
                                return true;
                            }
                        })
                        ->action(function($record){
                            if($record){
                                try {
                                    DB::beginTransaction(); 

                                    $teachers = json_decode($record?->teachers,true) ?? [];
                                    $user = auth()->user();
                                    $teachers[] = $user?->id;
                                    $record->teachers = json_encode($teachers);
                                    $record->save();

                                    DB::commit();

                                    Notification::make()
                                        ->title('Allow To Exam Success!')
                                        ->success()
                                        ->send();
                                } catch (\Throwable $th) {
                                    DB::rollBack();

                                    Notification::make()
                                        ->title('Allow To Exam Fail!')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),
                Action::make('not_allow_to_exam')
                        ->label('Not Allow To Exam')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Do you want to not allow to exam ?') // Confirmation modal title
                        ->icon('heroicon-o-academic-cap')
                        ->disabled(function($record){
                            $user = auth()->user();
                            if($user->isSuperAdmin()){
                                return true;
                            }
                            else{
                                if($record->isStudent()){
                                    $teachers = json_decode($record?->teachers,true);
                                    if($teachers){
                                        if(in_array($user?->id, $teachers)){
                                            return false;
                                        }
                                        return true;
                                    }
                                    return true;
                                }
                                return true;
                            }
                        })
                        ->hidden(function($record){
                            $user = auth()->user();
                            if($user->isSuperAdmin()){
                                return true;
                            }
                            else{
                                if($record->isStudent()){
                                    $teachers = json_decode($record?->teachers,true);
                                    if($teachers){
                                        if(in_array($user?->id, $teachers)){
                                            return false;
                                        }
                                        return true;
                                    }
                                    return true;
                                }
                                return true;
                            }
                        })
                        ->action(function($record){
                            if($record){
                                try {

                                    DB::beginTransaction(); 

                                    $teachers = json_decode($record?->teachers,true) ?? [];

                                    $user = auth()->user();
                                    $teachers = array_diff($teachers, [$user?->id]);
                                    $record->teachers = json_encode($teachers);
                                    $record->save();

                                    DB::commit();

                                    Notification::make()
                                        ->title('Not Allow To Exam Success!')
                                        ->success()
                                        ->send();
                                } catch (\Throwable $th) {
                                    DB::rollBack();

                                    Notification::make()
                                        ->title('Not Allow To Exam Fail!')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserRegistrations::route('/'),
            'create' => Pages\CreateUserRegistration::route('/create'),
            'edit' => Pages\EditUserRegistration::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // get users that are students
        $user_ids = DB::table('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_type', 'App\Models\User')
                        ->where('roles.name', config('access.role.student'))->pluck('model_has_roles.model_id');

        // get users
        if(auth()->user()->isSuperAdmin()){
            // get users that are students
            $user_ids = DB::table('model_has_roles')
                            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->where('model_has_roles.model_type', 'App\Models\User')
                            ->whereNotIn('roles.name', [config('access.role.student')])->pluck('model_has_roles.model_id');
        }
        else{

            // get users that are students
            $user_ids = DB::table('model_has_roles')
                            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->where('model_has_roles.model_type', 'App\Models\User')
                            ->where('roles.name', config('access.role.student'))->pluck('model_has_roles.model_id');
        }

        $query = User::whereIn('id', $user_ids);

        return $query;
    }
}
