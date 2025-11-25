<?php

namespace App\Filament\Resources\DomainListResource\Pages;

use App\Filament\Resources\DomainListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainList extends CreateRecord
{
    protected static string $resource = DomainListResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
