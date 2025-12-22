<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UrlBacklinkResource\Pages;
use App\Filament\Resources\UrlBacklinkResource\RelationManagers;
use App\Models\UrlBacklink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UrlBacklinkResource extends Resource
{
    protected static ?string $model = UrlBacklink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Tools';
    protected static ?string $navigationLabel = 'Set Backlinks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\TextInput::make('url')
                            ->label('URL Backlink')
                            ->required()
                            ->url()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(9),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUrlBacklinks::route('/'),
            'create' => Pages\CreateUrlBacklink::route('/create'),
            'edit' => Pages\EditUrlBacklink::route('/{record}/edit'),
        ];
    }
}
