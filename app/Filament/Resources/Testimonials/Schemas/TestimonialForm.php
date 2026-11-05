<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(80)->helperText('e.g. “Nusrat J.” – first name and initial is enough.'),
                TextInput::make('place')->maxLength(80),
                Select::make('rating')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])->default(5)->required(),
                TextInput::make('sort_order')->numeric()->default(0),
                Textarea::make('text')->label('Review')->required()->rows(3)->maxLength(600)->columnSpanFull(),
                Toggle::make('is_active')->label('Show on home page')->default(true),
            ]),
        ]);
    }
}
