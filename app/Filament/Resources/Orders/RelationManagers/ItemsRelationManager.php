<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Read-only: line items keep the name and price the customer paid. */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->columns([
                ImageColumn::make('image')->label('')->disk('media')->imageHeight(56)->imageWidth(42),
                TextColumn::make('name')->url(fn (OrderItem $record) => $record->product?->url())->openUrlInNewTab(),
                TextColumn::make('options')->state(fn (OrderItem $record) => collect([$record->color, $record->size])->filter()->implode(' · ')),
                TextColumn::make('quantity')->label('Qty'),
                TextColumn::make('unit_price')->money('BDT'),
                TextColumn::make('line_total')->label('Total')->state(fn (OrderItem $record) => $record->lineTotal())->money('BDT'),
            ]);
    }
}
