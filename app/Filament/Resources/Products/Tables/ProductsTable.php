<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('images')->label('')->disk('media')->limit(1)->imageHeight(56)->imageWidth(42),
                TextColumn::make('name')->searchable()->sortable()->description(fn (Product $record) => $record->category?->name),
                TextColumn::make('price')->money('BDT')->sortable()
                    ->description(fn (Product $record) => $record->onSale() ? 'Sale: ৳'.number_format($record->sale_price).' (−'.$record->discountPct().'%)' : null),
                TextColumn::make('sizes')->badge()->toggleable(),
                ToggleColumn::make('is_new')->label('New'),
                ToggleColumn::make('is_best_seller')->label('Best'),
                ToggleColumn::make('is_featured')->label('Featured'),
                ToggleColumn::make('is_active')->label('Live'),
                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name'),
                TernaryFilter::make('is_active')->label('Live'),
                TernaryFilter::make('on_sale')->label('On sale')
                    ->queries(true: fn ($q) => $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'), false: fn ($q) => $q->where(fn ($q) => $q->whereNull('sale_price')->orWhereColumn('sale_price', '>=', 'price'))),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-eye')->url(fn (Product $record) => $record->url())->openUrlInNewTab()->label('View'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('hide')->label('Hide from shop')->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
