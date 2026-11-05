<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Models\Faq;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('group')->label('Section')->required()->searchable()
                    ->options(fn () => Faq::query()->distinct()->orderBy('group')->pluck('group', 'group')->all())
                    ->createOptionForm([TextInput::make('group')->required()])->createOptionUsing(fn (array $data) => $data['group']),
                TextInput::make('sort_order')->numeric()->default(0),
                TextInput::make('question')->required()->maxLength(255)->columnSpanFull(),
                RichEditor::make('answer')->required()->toolbarButtons(['bold', 'italic', 'link', 'bulletList'])->columnSpanFull(),
                Toggle::make('is_active')->label('Show on FAQ page')->default(true),
            ]),
        ]);
    }
}
