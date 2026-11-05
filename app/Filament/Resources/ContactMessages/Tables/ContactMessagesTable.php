<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->description(fn ($record) => $record->phone)->weight(fn ($record) => $record->is_read ? null : 'bold'),
                TextColumn::make('message')->limit(90)->wrap()->searchable(),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
                ToggleColumn::make('is_read')->label('Handled'),
            ])
            ->filters([TernaryFilter::make('is_read')->label('Handled')])
            ->recordActions([EditAction::make()->label('Open')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
