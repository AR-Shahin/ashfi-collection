<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Customer & delivery')->columnSpan(2)->columns(2)->schema([
                TextInput::make('customer_name')->required()->maxLength(120),
                TextInput::make('phone')->tel()->required()->maxLength(20),
                TextInput::make('email')->email()->maxLength(190),
                Select::make('district')->options(array_combine(config('store.districts'), config('store.districts')))->searchable()->required(),
                Textarea::make('address')->rows(2)->required()->columnSpanFull(),
                Textarea::make('note')->label('Customer note')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Status')->columnSpan(1)->schema([
                Select::make('status')->options(Order::STATUSES)->required()->native(false)
                    ->helperText('Customers see this on their order page and on Track Order. Each change is time-stamped.'),
                Text::make(fn (?Order $record) => $record ? 'Placed '.$record->created_at->format('j M Y, g:i a') : ''),
            ]),
            Section::make('Payment')->columnSpan(1)->columnStart(3)->schema([
                Text::make(fn (?Order $record) => $record ? implode("\n", array_filter([
                    'Subtotal: ৳'.number_format($record->subtotal),
                    $record->discount ? 'Coupon '.$record->coupon_code.': −৳'.number_format($record->discount) : null,
                    'Delivery ('.($record->delivery_area === 'inside' ? 'inside Dhaka' : 'outside Dhaka').'): '.($record->delivery_charge ? '৳'.number_format($record->delivery_charge) : 'Free'),
                    'Total: ৳'.number_format($record->total),
                    $record->paymentLabel(),
                ])) : ''),
            ]),
        ]);
    }
}
