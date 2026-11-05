<?php

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->defaultGroup(Group::make('group')->label('Section'))
            ->columns([
                TextColumn::make('question')->searchable()->wrap(),
                ToggleColumn::make('is_active')->label('Live'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
