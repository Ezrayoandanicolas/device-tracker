<?php

namespace App\Filament\Resources;

use App\Models\Shortlink;
use App\Models\DomainList;
use App\Filament\Resources\ShortlinkResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ShortlinkResource extends Resource
{
    protected static ?string $model = Shortlink::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'Redirect Manager';
    protected static ?string $navigationLabel = 'Shortlinks';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            Forms\Components\Select::make('domain_list_id')
                ->label('Domain')
                ->options(DomainList::where('is_active', true)->pluck('domain', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->suffixAction(
                    Forms\Components\Actions\Action::make('generate')
                        ->icon('heroicon-o-plus')
                        ->action(fn ($set) => $set('slug', Str::random(6)))
                )
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('target_url')
                ->label('Target URL')
                ->placeholder('https://google.com')
                ->required()
                ->url(),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->inline(false),

            Forms\Components\TextInput::make('hit_count')
                ->label('Total Hits')
                ->disabled(),

            Forms\Components\TextInput::make('last_hit_at')
                ->label('Last Hit')
                ->disabled(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('domain.domain')
                    ->label('Domain')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_link')
                    ->label('Short URL')
                    ->getStateUsing(fn ($record) => url("/go/{$record->slug}"))
                    ->copyable(),

                Tables\Columns\TextColumn::make('target_url')
                    ->label('Target')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->target_url),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('hit_count')
                    ->label('Hits')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_hit_at')
                    ->label('Last Hit')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortlinks::route('/'),
            'create' => Pages\CreateShortlink::route('/create'),
            'edit' => Pages\EditShortlink::route('/{record}/edit'),
        ];
    }
}
