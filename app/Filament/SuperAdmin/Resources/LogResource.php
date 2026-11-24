<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\LogResource\Pages;
use App\Models\Log;
use App\Models\Tenant;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LogResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 200;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options(Log::typeOptions())
                    ->required()
                    ->default(Log::TYPE_INFO),
                Forms\Components\TextInput::make('tenant_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('model_type')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('model_id')
                    ->numeric()
                    ->default(null),
                Placeholder::make('data_json')
                    ->content(fn (Log $log): ?string => $log->getDataJson()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Log::TYPE_INFO => 'info',
                        Log::TYPE_WARNING => 'warning',
                        Log::TYPE_ERROR => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('tenant_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_reference')
                    ->label('Model')
                    ->getStateUsing(function (Log $record): ?string {
                        if (blank($record->model_type) || blank($record->model_id)) {
                            return null;
                        }

                        return class_basename($record->model_type).'/'.$record->model_id;
                    })
                    ->description(fn (Log $record): ?string => static::resolveModelName($record))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search) {
                            $query->where('model_type', 'like', "%{$search}%")
                                ->orWhere('model_id', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('model_type', $direction)
                            ->orderBy('model_id', $direction);
                    }),
                Tables\Columns\TextColumn::make('data_json')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(function (Log $record): ?string {
                        if (blank($record->data_json)) {
                            return null;
                        }

                        $decoded = json_decode($record->data_json);

                        return json_last_error() === JSON_ERROR_NONE
                            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            : $record->data_json;
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->placeholder('All types')
                    ->options(Log::typeOptions())
                    ->native(false),
                SelectFilter::make('action')
                    ->label('Action')
                    ->placeholder('All actions')
                    ->options(fn (): array => Log::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->toArray()
                    )
                    ->native(false)
                    ->searchable(),
                SelectFilter::make('model_type')
                    ->label('Model Type')
                    ->placeholder('All model types')
                    ->options(fn (): array => Log::query()
                        ->whereNotNull('model_type')
                        ->where('model_type', '!=', '')
                        ->distinct()
                        ->orderBy('model_type')
                        ->pluck('model_type', 'model_type')
                        ->toArray()
                    )
                    ->native(false)
                    ->searchable(),
                Filter::make('model_id')
                    ->label('Model ID')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('')
                            ->numeric()
                            ->placeholder('e.g. 123')
                            ->maxLength(20),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if (blank($value)) {
                            return $query;
                        }

                        return $query->where('model_id', $value);
                    }),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc');
    }

    protected static function resolveModelName(Log $record): ?string
    {
        if (blank($record->model_type) || blank($record->model_id)) {
            return null;
        }

        static $cache = [];
        $cacheKey = $record->model_type.':'.$record->model_id;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $modelType = $record->model_type;

        if (! is_string($modelType) || ! class_exists($modelType)) {
            return $cache[$cacheKey] = null;
        }

        if (! is_subclass_of($modelType, Model::class)) {
            return $cache[$cacheKey] = null;
        }

        $model = $modelType::find($record->model_id);
        if (! $model) {
            return $cache[$cacheKey] = null;
        }

        $name = match ($modelType) {
            User::class => $model->email,
            Tenant::class => $model->name,
            default => $model->name ?? null,
        };

        return $cache[$cacheKey] = filled($name) ? (string) $name : null;
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
            'index' => Pages\ListLogs::route('/'),
            'create' => Pages\CreateLog::route('/create'),
            'edit' => Pages\EditLog::route('/{record}/edit'),
        ];
    }
}
