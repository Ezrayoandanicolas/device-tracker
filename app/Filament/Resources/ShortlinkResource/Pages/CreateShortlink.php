<?php

namespace App\Filament\Resources\ShortlinkResource\Pages;

use App\Filament\Resources\ShortlinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShortlink extends CreateRecord
{
    protected static string $resource = ShortlinkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
