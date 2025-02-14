<?php

namespace App\Filament\Resources;

use App\Filament\BaseClasses\BaseUserResource;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\TenantUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?int $navigationSort = 10;


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('tenants')
            ->where('users.is_active', true);

        return $query;
    }

    public static function form(Form $form): Form
    {
        return BaseUserResource::form($form, false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['name', 'middle_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                /*
                 * only users.is_active = true
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                */

                Tables\Columns\TextColumn::make('tenant_user_pivot.last_login_at')
                    ->label('Last login'),

                Tables\Columns\IconColumn::make('tenant_user_pivot.is_active_on_tenant')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('profile_photo')
                    ->collection('profile')
                    ->conversion('thumb')
                    ->label('Photo'),
                    // ->thumbnail()
                    // ->maxWidth('50px')
                    //->maxHeight('50px')
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('full_name')->query(function (Builder $query, $value) {
                    $query->where('name', 'like', "%{$value}%")
                        ->orWhere('middle_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
