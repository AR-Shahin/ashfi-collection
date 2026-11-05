<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/** Brand details, announcement bar and delivery charges (stored in the settings table). */
class StoreSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Storefront';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Store settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'brand' => setting('brand'),
            'announcements' => collect(setting('announcements', []))->map(fn ($text) => ['text' => $text])->all(),
            'delivery' => setting('delivery'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Delivery charges')->columns(3)->schema([
                TextInput::make('delivery.insideDhaka')->label('Inside Dhaka')->numeric()->minValue(0)->prefix('৳')->required(),
                TextInput::make('delivery.outsideDhaka')->label('Outside Dhaka')->numeric()->minValue(0)->prefix('৳')->required(),
                TextInput::make('delivery.freeOver')->label('Free delivery over')->numeric()->minValue(0)->prefix('৳')->required(),
            ]),
            Section::make('Announcement bar')->description('Messages rotate at the very top of every page.')->schema([
                Repeater::make('announcements')->hiddenLabel()->schema([TextInput::make('text')->hiddenLabel()->required()->maxLength(120)])
                    ->reorderable()->addActionLabel('Add message')->defaultItems(1)->maxItems(6),
            ]),
            Section::make('Brand & contact')->columns(2)->schema([
                TextInput::make('brand.name')->label('Store name')->required(),
                TextInput::make('brand.url')->label('Website')->url(),
                TextInput::make('brand.tagline')->label('Tagline'),
                TextInput::make('brand.taglineBn')->label('Tagline (Bangla)'),
                TextInput::make('brand.phone')->label('Phone')->tel(),
                TextInput::make('brand.whatsapp')->label('WhatsApp number')->helperText('Digits only with country code, e.g. 8801700000000'),
                TextInput::make('brand.email')->label('Email')->email(),
                TextInput::make('brand.hours')->label('Opening hours'),
                TextInput::make('brand.address')->label('Address')->columnSpanFull(),
                TextInput::make('brand.facebook')->label('Facebook page')->url(),
                TextInput::make('brand.instagram')->label('Instagram')->url(),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([Actions::make([Action::make('save')->label('Save settings')->submit('save')->keyBindings(['mod+s'])])]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::put('brand', $data['brand']);
        Setting::put('announcements', collect($data['announcements'] ?? [])->pluck('text')->filter()->values()->all());
        Setting::put('delivery', array_map('intval', $data['delivery']));

        Notification::make()->title('Settings saved')->success()->send();
    }
}
