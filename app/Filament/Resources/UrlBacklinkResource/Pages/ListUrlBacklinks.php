<?php

namespace App\Filament\Resources\UrlBacklinkResource\Pages;

use App\Filament\Resources\UrlBacklinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUrlBacklinks extends ListRecords
{
    protected static string $resource = UrlBacklinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
