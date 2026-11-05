<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $live = Order::where('status', '!=', 'cancelled');
        $month = (clone $live)->where('created_at', '>=', now()->startOfMonth());

        return [
            Stat::make('New orders', Order::where('status', 'placed')->count())->description('Waiting for a confirmation call')
                ->color('warning')->url(route('filament.admin.resources.orders.index', ['filters' => ['status' => ['values' => ['placed']]]])),
            Stat::make('Sales this month', '৳'.number_format((clone $month)->sum('total')))->description((clone $month)->count().' orders'),
            Stat::make('To deliver', Order::whereIn('status', ['confirmed', 'shipped'])->count())->description('Confirmed or shipped'),
            Stat::make('Customers', User::where('is_admin', false)->count())
                ->description(ContactMessage::where('is_read', false)->count().' unread messages'),
        ];
    }
}
