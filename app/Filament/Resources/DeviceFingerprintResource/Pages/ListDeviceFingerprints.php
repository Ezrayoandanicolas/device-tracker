<?php

namespace App\Filament\Resources\DeviceFingerprintResource\Pages;

use App\Filament\Resources\DeviceFingerprintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeviceFingerprints extends ListRecords
{
    protected static string $resource = DeviceFingerprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
