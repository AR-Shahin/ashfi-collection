<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest orders')
            ->query(Order::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('number')->weight('bold'),
                TextColumn::make('customer_name')->label('Customer')->description(fn (Order $record) => $record->phone),
                TextColumn::make('total')->money('BDT'),
                TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)->color(fn ($state) => OrdersTable::statusColor($state)),
                TextColumn::make('created_at')->label('Placed')->since(),
            ])
            ->recordActions([Action::make('open')->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))]);
    }
}
