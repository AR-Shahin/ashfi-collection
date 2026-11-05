<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')->label('')->disk('media')->imageHeight(56)->imageWidth(42),
                TextColumn::make('name')->searchable()->description(fn ($record) => $record->blurb),
                TextColumn::make('slug')->color('gray'),
                TextColumn::make('products_count')->counts('products')->label('Products')->badge(),
                ToggleColumn::make('is_active')->label('Live'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
