<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->description(fn ($record) => $record->place),
                TextColumn::make('rating')->formatStateUsing(fn ($state) => str_repeat('★', (int) $state)),
                TextColumn::make('text')->limit(80)->wrap(),
                ToggleColumn::make('is_active')->label('Live'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
