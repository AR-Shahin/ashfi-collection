<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function statusColor(string $status): string
    {
        return ['placed' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'][$status] ?? 'gray';
    }

    public static function configure(Table $table): Table
    {
        $next = ['placed' => 'confirmed', 'confirmed' => 'shipped', 'shipped' => 'delivered'];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')->searchable()->weight('bold')->copyable(),
                TextColumn::make('customer_name')->label('Customer')->searchable()->description(fn (Order $record) => $record->phone),
                TextColumn::make('district')->toggleable(),
                TextColumn::make('items_count')->counts('items')->label('Items'),
                TextColumn::make('total')->money('BDT')->sortable(),
                TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)->color(fn ($state) => self::statusColor($state)),
                TextColumn::make('created_at')->label('Placed')->dateTime('j M Y, g:i a')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Order::STATUSES)->multiple(),
                Filter::make('today')->label('Placed today')->query(fn (Builder $q) => $q->whereDate('created_at', today())),
            ])
            ->recordActions([
                Action::make('advance')
                    ->label(fn (Order $record) => isset($next[$record->status]) ? 'Mark '.Order::STATUSES[$next[$record->status]] : '')
                    ->icon('heroicon-o-arrow-right-circle')->color('success')
                    ->visible(fn (Order $record) => isset($next[$record->status]))
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => $record->update(['status' => $next[$record->status]])),
                EditAction::make()->label('Open'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('status')->label('Change status')->icon('heroicon-o-arrow-path')
                        ->schema([Select::make('status')->options(Order::STATUSES)->required()])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
