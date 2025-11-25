<?php

namespace App\Filament\Resources\DomainListResource\Pages;

use App\Filament\Resources\DomainListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomainLists extends ListRecords
{
    protected static string $resource = DomainListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
