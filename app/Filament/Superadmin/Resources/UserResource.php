<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\BaseClasses\BaseUserResource;
use App\Filament\Superadmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 20;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return BaseUserResource::form($schema, true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['name', 'middle_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('tenant')
                    ->label('Tenants')
                    ->html()
                    ->getStateUsing(function (User $record) {
                        $html = '';
                        foreach ($record->tenants as $tenant) {
                            $html .= sprintf('<div class="badge badge-primary"><a href="%s">%s</a?</div>',
                                route('filament.superadmin.resources.tenants.edit', ['record' => $tenant->id]),
                                $tenant->name
                            );
                        }

                        return $html;
                    }),

                Tables\Columns\IconColumn::make('is_super_admin')
                    ->boolean(),
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
                //
            ])
            ->actions([
                Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Filament\Actions\BulkActionGroup::make([
                    Filament\Actions\DeleteBulkAction::make(),
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
