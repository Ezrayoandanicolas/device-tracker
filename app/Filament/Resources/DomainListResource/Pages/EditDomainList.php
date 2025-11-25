<?php

namespace App\Filament\Resources\DomainListResource\Pages;

use App\Filament\Resources\DomainListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDomainList extends EditRecord
{
    protected static string $resource = DomainListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
