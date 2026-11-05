<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image_lg')->label('')->disk('media')->imageHeight(48)->imageWidth(92),
                TextColumn::make('title')->description(fn ($record) => $record->motto)->searchable(),
                IconColumn::make('video_lg')->label('Video')->boolean()->getStateUsing(fn ($record) => filled($record->video_lg)),
                TextColumn::make('transition')->badge(),
                TextColumn::make('duration')->suffix(' ms'),
                ToggleColumn::make('is_active')->label('Live'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
