<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiteResource\Pages\CreateSite;
use App\Filament\Admin\Resources\SiteResource\Pages\EditSite;
use App\Filament\Admin\Resources\SiteResource\Pages\ListSites;
use App\Models\Site;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource; // Important for v4/v5 compatibility
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    // Fixed Heroicon access for v5
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sites';

    protected static ?int $navigationSort = 2;

    /**
     * To fix the "Not compatible" error, we must use the Schema typehint
     * that the base Resource class expects in this version.
     */
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
                    ->url()
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_online')
                    ->label('Online')
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
                IconColumn::make('is_online')
                    ->boolean()
                    ->label('Online'),
                TextColumn::make('status_code')
                    ->label('Status'),
                TextColumn::make('uptime')
                    ->suffix('%')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('response_time')
                    ->suffix('ms')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('last_checked_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_online')
                    ->label('Online status')
                    ->placeholder('All sites')
                    ->trueLabel('Online')
                    ->falseLabel('Offline'),
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
