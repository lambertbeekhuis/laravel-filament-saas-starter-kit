<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 210;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('connection')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('queue')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('payload')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('exception')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('failed_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*
                Tables\Columns\TextColumn::make('uuid')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('connection')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('queue')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payload')
                    ->searchable()
                    ->sortable(),
                */
                Tables\Columns\TextColumn::make('exception')
                    ->limit(100)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->emptyStateActions([
                Filament\Actions\CreateAction::make(),
            ])
            ->defaultPaginationPageOption(50)
            ->defaultSort('failed_at', 'desc');
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
            'index' => Pages\ListFailedJobs::route('/'),
            'edit' => Pages\EditFailedJobs::route('/{record}/edit'),
        ];
    }
}
