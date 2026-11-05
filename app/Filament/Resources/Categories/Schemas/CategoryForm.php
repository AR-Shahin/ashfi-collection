<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(80)->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set, ?string $state, string $operation) => $operation === 'create' && blank($get('slug')) ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')->required()->alphaDash()->unique(ignoreRecord: true)->helperText('Used in shop links: /shop?category=slug'),
                TextInput::make('blurb')->maxLength(120)->helperText('Short line shown under the title, e.g. “Timeless drapes”.'),
                TextInput::make('sort_order')->numeric()->default(0),
                FileUpload::make('image')->image()->disk('media')->directory('uploads/categories')->visibility('public')
                    ->imageEditor()->imageEditorAspectRatioOptions(['3:4'])->maxSize(4096)->helperText('Portrait image (3:4) for the home page tile.'),
                Toggle::make('is_active')->label('Show on site')->default(true),
            ]),
        ]);
    }
}
