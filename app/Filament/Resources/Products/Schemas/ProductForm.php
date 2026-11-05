<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Grid::make(1)->columnSpan(2)->schema([
                Section::make('Product')->columns(2)->schema([
                    TextInput::make('name')->required()->maxLength(190)->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set, ?string $state, string $operation) => $operation === 'create' && blank($get('slug')) ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('slug')->required()->maxLength(190)->unique(ignoreRecord: true)->alphaDash()
                        ->helperText('Used in the product URL: /product/your-slug'),
                    Select::make('category_id')->relationship('category', 'name')->required()->searchable()->preload()
                        ->createOptionForm([TextInput::make('name')->required(), TextInput::make('slug')->required()->alphaDash()]),
                    TextInput::make('short')->label('Short description')->maxLength(300)->helperText('One line shown in quick view and search results.'),
                    Textarea::make('description')->rows(4)->columnSpanFull(),
                ]),
                Section::make('Photos')->schema([
                    FileUpload::make('images')->hiddenLabel()->image()->multiple()->reorderable()->appendFiles()
                        ->disk('media')->directory('uploads/products')->visibility('public')
                        ->imageEditor()->imageEditorAspectRatioOptions(['3:4'])->maxSize(4096)->maxFiles(8)->panelLayout('grid')
                        ->helperText('Portrait photos work best (3:4, at least 900×1200). The first photo is the main image.'),
                ]),
                Section::make('Options')->columns(2)->schema([
                    TagsInput::make('sizes')->placeholder('Add a size, e.g. M')->helperText('Leave one value such as "Free Size" when there is no size choice.')
                        ->suggestions(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size']),
                    Repeater::make('colors')->schema([
                        TextInput::make('name')->required()->maxLength(40),
                        ColorPicker::make('hex')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                    ])->columns(2)->defaultItems(1)->reorderable()->itemLabel(fn (array $state) => $state['name'] ?? null)->collapsible(),
                ]),
                Section::make('Details')->description('Shown in the “Product Details” panel. Add a “Care” row for care instructions.')->schema([
                    Repeater::make('details')->hiddenLabel()->schema([
                        TextInput::make('label')->required()->maxLength(60),
                        TextInput::make('value')->required()->maxLength(255),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add detail'),
                ]),
            ]),
            Grid::make(1)->columnSpan(1)->schema([
                Section::make('Price')->schema([
                    TextInput::make('price')->label('Regular price')->required()->numeric()->minValue(0)->prefix('৳'),
                    TextInput::make('sale_price')->label('Sale price')->numeric()->minValue(0)->prefix('৳')->lt('price')
                        ->helperText('Leave empty when the product is not on sale.'),
                ]),
                Section::make('Visibility')->schema([
                    Toggle::make('is_active')->label('Show in shop')->default(true),
                    Toggle::make('is_new')->label('New arrival'),
                    Toggle::make('is_best_seller')->label('Best seller'),
                    Toggle::make('is_featured')->label('Featured on home page')->live(),
                    TextInput::make('featured_order')->numeric()->default(0)->visible(fn (Get $get) => $get('is_featured'))
                        ->helperText('Lower numbers appear first.'),
                ]),
            ]),
        ]);
    }
}
