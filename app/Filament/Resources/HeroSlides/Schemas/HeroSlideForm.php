<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use App\Models\HeroSlide;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        $image = fn (string $name, string $label, string $help) => FileUpload::make($name)->label($label)->image()
            ->disk('media')->directory('uploads/hero')->visibility('public')->maxSize(6144)->helperText($help);
        $video = fn (string $name, string $label, string $help) => FileUpload::make($name)->label($label)
            ->acceptedFileTypes(['video/mp4', 'video/webm'])->disk('media')->directory('uploads/hero')->visibility('public')->maxSize(20480)->helperText($help);

        return $schema->columns(3)->components([
            Section::make('Text')->columnSpan(2)->columns(2)->schema([
                TextInput::make('motto')->maxLength(120)->helperText('Small script line above the title, e.g. the site motto.'),
                Select::make('motto_lang')->label('Motto language')->options(['' => 'English', 'bn' => 'Bangla'])->default(''),
                TextInput::make('title')->required()->maxLength(120)->columnSpanFull(),
                Textarea::make('description')->rows(2)->maxLength(300)->columnSpanFull(),
                TextInput::make('primary_label')->label('Main button text')->maxLength(40),
                TextInput::make('primary_url')->label('Main button link')->maxLength(255)->placeholder('/shop?category=saree'),
                TextInput::make('secondary_label')->label('Second button text')->maxLength(40),
                TextInput::make('secondary_url')->label('Second button link')->maxLength(255)->placeholder('/offers'),
            ]),
            Section::make('Display')->columnSpan(1)->schema([
                Toggle::make('is_active')->label('Show this slide')->default(true),
                Select::make('transition')->options(HeroSlide::TRANSITIONS)->default('curtain')->required(),
                Select::make('align')->label('Text position')->options(['left' => 'Left', 'center' => 'Centre', 'right' => 'Right'])->default('left')->required(),
                TextInput::make('duration')->numeric()->suffix('ms')->default(7000)->minValue(3000)->maxValue(20000),
                TextInput::make('sort_order')->numeric()->default(0),
            ]),
            Section::make('Background')->columnSpanFull()->columns(3)->description('Upload three crops of the same picture so phones download a small file. A video plays over the image when set.')->schema([
                $image('image_lg', 'Desktop image', '1920×1000 (wide)')->required(),
                $image('image_md', 'Tablet image', '1200×900'),
                $image('image_sm', 'Phone image', '750×1100 (portrait)'),
                TextInput::make('image_alt')->label('Image description (alt text)')->maxLength(200)->columnSpanFull(),
                $video('video_lg', 'Background video (optional)', 'MP4, muted loop, under 6 MB'),
                $video('video_sm', 'Phone video (optional)', 'Smaller MP4 for phones'),
            ]),
        ]);
    }
}
