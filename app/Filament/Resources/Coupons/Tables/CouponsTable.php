<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Models\Coupon;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->weight('bold')->copyable()->description(fn (Coupon $record) => $record->title),
                TextColumn::make('type')->badge()->formatStateUsing(fn ($state, Coupon $record) => match ($state) {
                    'percent' => $record->value.'% off'.($record->max_discount ? ' (max ৳'.number_format($record->max_discount).')' : ''),
                    'flat' => '৳'.number_format($record->value).' off',
                    default => 'Free delivery',
                }),
                TextColumn::make('min_spend')->label('Min. spend')->money('BDT'),
                TextColumn::make('expires_at')->label('Valid until')->date()->sortable()
                    ->color(fn (Coupon $record) => $record->isExpired() ? 'danger' : null)->placeholder('No expiry'),
                TextColumn::make('uses')->label('Orders')->state(fn (Coupon $record) => Order::where('coupon_code', $record->code)->where('status', '!=', 'cancelled')->count())->badge(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
