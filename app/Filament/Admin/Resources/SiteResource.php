<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\SiteStatus;
use App\Filament\Admin\Resources\SiteResource\Pages\CreateSite;
use App\Filament\Admin\Resources\SiteResource\Pages\EditSite;
use App\Filament\Admin\Resources\SiteResource\Pages\ListSites;
use App\Models\Site;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sites';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->maxLength(255)
                    ->rules(['url_or_domain'])
                    ->placeholder('example.com or https://example.com'),
                ToggleButtons::make('status')
                    ->label('Status')
                    ->options(SiteStatus::class)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->searchable()
                    ->url(fn (Site $record): string => $record->url),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => SiteStatus::Online,
                        'danger' => SiteStatus::Offline,
                        'warning' => SiteStatus::Checking,
                    ])
                    ->formatStateUsing(fn (SiteStatus $state): string => $state->label()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        SiteStatus::Online->value => SiteStatus::Online->label(),
                        SiteStatus::Offline->value => SiteStatus::Offline->label(),
                        SiteStatus::Checking->value => SiteStatus::Checking->label(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }
}
