<?php

namespace App\Filament\Superadmin\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('middle_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255)
                    ->default(null),

                Forms\Components\SpatieMediaLibraryFileUpload::make('profile_photo')
                    ->collection('profile')
                    // ->rules(['required'])
                    ->image()
                //->fit(Fit::contain())
                //->prunable()
                //->preview()
                ,

                /*
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                */

                Forms\Components\DatePicker::make('date_of_birth'),

                /*
                Forms\Components\Toggle::make('client_user.is_active'),

                Forms\Components\Toggle::make('client_user.is_admin'),
                */

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['name', 'middle_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('profile_photo')
                    ->collection('profile')
                    ->conversion('thumb')
                    ->label('Photo')
                    // ->thumbnail()
                    // ->maxWidth('50px')
                    //->maxHeight('50px')
                    ->searchable(),
                Tables\Columns\IconColumn::make('client_user.is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('client_user.is_admin')
                    ->label('Admin')
                    ->boolean(),
                Tables\Columns\TextColumn::make('client_user.last_login_at')
                    ->label('Last login'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['password'] = 'to be generated';
                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



}
