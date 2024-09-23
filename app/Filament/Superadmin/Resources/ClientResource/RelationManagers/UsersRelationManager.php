<?php

namespace App\Filament\Superadmin\Resources\ClientResource\RelationManagers;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SentInvitationToUserNotification;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {


        return $form
            ->schema([
                Forms\Components\Hidden::make('client_id')
                    ->default($this->getOwnerRecord()->id), // used for sending email
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
                    ->image(),

                /*
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                */

                Forms\Components\DatePicker::make('date_of_birth'),

                Forms\Components\Toggle::make('sent_invitation')
                    ->label('Send invitation email')
                    // might be made hidden of already logged in once
                    ->default(false),
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
                    ->label('Photo'),
                    // ->thumbnail()
                    // ->maxWidth('50px')
                    //->maxHeight('50px'),
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
                    ->using(function ($record, array $data) {
                        $user = User::create($data);
                        $clientUser = ClientUser::create(['client_id' => $data['client_id'], 'user_id' => $user->id]);
                        if ($data['sent_invitation'] ?? false) {
                            Notification::send($user, new SentInvitationToUserNotification($user, Client::find($data['client_id'])));
                        }
                        return $user;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->using(function ($record, array $data) {
                        $record->update($data);
                        if ($data['sent_invitation'] ?? false) {
                            Notification::send($record, new SentInvitationToUserNotification($record, Client::find($data['client_id'])));
                        }
                        return $record;
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
