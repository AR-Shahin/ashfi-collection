<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->disabled(),
                TextInput::make('phone')->disabled()->suffixAction(null),
                TextInput::make('email')->disabled(),
                TextInput::make('created_at')->label('Received')->disabled(),
                Textarea::make('message')->disabled()->rows(6)->columnSpanFull(),
                Toggle::make('is_read')->label('Handled'),
            ]),
        ]);
    }
}
