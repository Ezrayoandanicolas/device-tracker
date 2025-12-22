<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BacklinkArticleResource\Pages;
use App\Filament\Resources\BacklinkArticleResource\RelationManagers;
use App\Models\BacklinkArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BacklinkArticleResource extends Resource
{
    protected static ?string $model = BacklinkArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = 'Backlink Article';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('article_domain')
                    ->label('Domain')
                    ->searchable(),

                Tables\Columns\TextColumn::make('article_slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('backlink.url')
                    ->label('Backlink')
                    ->url(fn ($record) => $record->backlink->url)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->defaultSort('views', 'desc');
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
            'index' => Pages\ListBacklinkArticles::route('/'),
            'create' => Pages\CreateBacklinkArticle::route('/create'),
            'edit' => Pages\EditBacklinkArticle::route('/{record}/edit'),
        ];
    }
}
