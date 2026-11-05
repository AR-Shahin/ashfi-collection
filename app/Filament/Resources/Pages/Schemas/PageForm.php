<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(120),
                TextInput::make('slug')->required()->disabled(fn (string $operation) => $operation === 'edit')->alphaDash()->unique(ignoreRecord: true)
                    ->helperText('size-guide, terms and policy are used by the site.'),
                TextInput::make('lead')->label('Subtitle')->maxLength(200)->columnSpanFull(),
                Textarea::make('meta_description')->label('Search engine description')->rows(2)->maxLength(300)->columnSpanFull(),
                RichEditor::make('body')->columnSpanFull()
                    ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['table'], ['undo', 'redo']])
                    ->helperText('On the Policies page the delivery table and photo credits are added automatically above and below this text.'),
            ]),
        ]);
    }
}
