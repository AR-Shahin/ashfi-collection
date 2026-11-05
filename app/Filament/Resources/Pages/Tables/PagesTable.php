<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->description(fn ($record) => '/'.$record->slug),
                TextColumn::make('updated_at')->label('Last edited')->since()->sortable(),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-eye')->url(fn ($record) => url('/'.$record->slug))->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }
}
