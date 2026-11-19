<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                BadgeColumn::make('role')
                    ->searchable()
                    ->colors([
                        'primary' => User::ROLE_ADMIN,
                        'success' => User::ROLE_OWNER,
                        'info' => User::ROLE_AGENT,
                        'warning' => User::ROLE_CARETAKER,
                        'gray' => User::ROLE_TENANT,
                        'secondary' => User::ROLE_PROPERTY_MANAGER,
                        'danger' => User::ROLE_DEVELOPER,
                    ]),
                BadgeColumn::make('status')
                    ->searchable()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
