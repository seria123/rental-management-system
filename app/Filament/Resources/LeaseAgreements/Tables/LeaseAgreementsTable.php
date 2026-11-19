<?php

namespace App\Filament\Resources\LeaseAgreements\Tables;

use App\Filament\Resources\LeaseAgreements\Pages\ViewLeaseAgreement;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaseAgreementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lease.id')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->searchable(),
                TextColumn::make('original_filename')
                    ->searchable(),
                IconColumn::make('owner_signed')
                    ->boolean(),
                IconColumn::make('tenant_signed')
                    ->boolean(),
                TextColumn::make('owner_signed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tenant_signed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('owner_signature_ip')
                    ->searchable(),
                TextColumn::make('tenant_signature_ip')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->url(fn ($record) => ViewLeaseAgreement::getUrl(['record' => $record])),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
