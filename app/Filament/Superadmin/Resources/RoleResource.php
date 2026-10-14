<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\RoleResource\Pages;
use App\Models\Role;
use App\Support\Features;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?int $navigationSort = 90;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return Features::permissionsEnabled();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                /*
                Forms\Components\TextInput::make('tenant_id')
                    ->numeric()
                    ->default(null),
                */
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('permissions')
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->multiple()
                    ->multiple(),
                Forms\Components\Select::make('guard_name')
                    ->options([
                        'web' => 'web',
                        // 'api' => 'API',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions')
                    ->label('Related Permissions')
                    ->html()
                    ->getStateUsing(function (Model $record) {
                        $html = '';
                        foreach ($record->permissions as $permission) {
                            $html .= sprintf('<div class="badge badge-primary">%s</div>', $permission->name);
                        }

                        return $html;
                    }),
                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
