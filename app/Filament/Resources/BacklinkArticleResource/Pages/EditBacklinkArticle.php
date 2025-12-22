<?php

namespace App\Filament\Resources\BacklinkArticleResource\Pages;

use App\Filament\Resources\BacklinkArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBacklinkArticle extends EditRecord
{
    protected static string $resource = BacklinkArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
