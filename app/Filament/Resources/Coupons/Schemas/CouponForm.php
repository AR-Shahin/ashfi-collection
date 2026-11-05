<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->required()->maxLength(40)->unique(ignoreRecord: true)->alphaDash()
                    ->dehydrateStateUsing(fn ($state) => strtoupper(trim($state)))->helperText('Customers type this at checkout. Letters and numbers only.'),
                Select::make('type')->options(Coupon::TYPES)->required()->live()->default('percent'),
                TextInput::make('value')->numeric()->minValue(0)->default(0)
                    ->label(fn (Get $get) => $get('type') === 'percent' ? 'Percent off' : 'Amount off')
                    ->suffix(fn (Get $get) => $get('type') === 'percent' ? '%' : '৳')
                    ->maxValue(fn (Get $get) => $get('type') === 'percent' ? 100 : null)
                    ->hidden(fn (Get $get) => $get('type') === 'shipping')->required(fn (Get $get) => $get('type') !== 'shipping'),
                TextInput::make('max_discount')->label('Maximum discount')->numeric()->prefix('৳')->visible(fn (Get $get) => $get('type') === 'percent')
                    ->helperText('Optional cap for percentage coupons.'),
                TextInput::make('min_spend')->label('Minimum spend')->numeric()->prefix('৳')->default(0)->required(),
                DatePicker::make('expires_at')->label('Valid until')->native(false),
                TextInput::make('title')->maxLength(80)->helperText('Shown on the Offers page and in the cart, e.g. “10% off your order”.'),
                TextInput::make('detail')->maxLength(200)->helperText('One-line conditions, e.g. “Up to ৳500 off on orders of ৳1,500 or more.”'),
                Toggle::make('is_active')->label('Active')->default(true),
            ]),
        ]);
    }
}
