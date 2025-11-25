<?php

namespace App\Filament\Resources;

use App\Models\DomainList;
use App\Filament\Resources\DomainListResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class DomainListResource extends Resource
{
    protected static ?string $model = DomainList::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Domain Redirect';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('domain')
                    ->label('Domain')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Forms\Components\Toggle::make('is_blocked')
                    ->label('Blokir Domain')
                    ->default(false)
                    ->reactive(),

                Forms\Components\TextInput::make('blocked_reason')
                    ->label('Alasan Blokir')
                    ->visible(fn(callable $get) => $get('is_blocked'))
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\IconColumn::make('is_blocked')
                    ->label('Diblokir?')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check'),

                Tables\Columns\TextColumn::make('blocked_reason')
                    ->label('Alasan')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Hanya yang Aktif')
                    ->query(fn ($query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('blocked')
                    ->label('Domain Diblokir')
                    ->query(fn ($query) => $query->where('is_blocked', true)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomainLists::route('/'),
            'create' => Pages\CreateDomainList::route('/create'),
            'edit' => Pages\EditDomainList::route('/{record}/edit'),
        ];
    }
}
