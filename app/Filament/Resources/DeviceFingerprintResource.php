<?php

namespace App\Filament\Resources;

use App\Models\DeviceFingerprint;
use App\Filament\Resources\DeviceFingerprintResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;

class DeviceFingerprintResource extends Resource
{
    protected static ?string $model = DeviceFingerprint::class;
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = 'Device Fingerprints';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('fingerprint_id')
                    ->label('Fingerprint ID')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Last IP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('scan_count')
                    ->label('Scan Count')
                    ->sortable(),

                Tables\Columns\TextColumn::make('similarity_score')
                    ->label('Similarity')
                    ->formatStateUsing(fn ($state) => $state ? ($state * 100) . '%' : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('First Seen')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Seen')
                    ->dateTime(),

                // Menampilkan berapa device menggunakan LAST IP untuk row ini
                Tables\Columns\TextColumn::make('ip_usage')
                    ->label('Devices Using IP')
                    ->getStateUsing(function ($record) {
                        // Jika ip_address kosong, tampilkan '-'
                        if (empty($record->ip_address)) {
                            return '-';
                        }
                        return \App\Models\DeviceFingerprint::whereHas('visitLogs', function ($q) use ($record) {
                            $q->where('ip_address', $record->ip_address);
                        })->count();
                    })
                    ->badge()
                    ->color('primary'),
            ])

            ->filters([
                // Filter by IP using VisitLogs relation
                Tables\Filters\Filter::make('ip_used')
                    ->label('Filter by IP')
                    ->form([
                        Forms\Components\TextInput::make('ip')
                            ->label('IP Address')
                            ->placeholder('e.g. 36.71.22.120'),
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['ip'])) {
                            return $query;
                        }
                        return $query->whereHas('visitLogs', function ($q) use ($data) {
                            $q->where('ip_address', $data['ip']);
                        });
                    }),
            ])

            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceFingerprints::route('/'),
        ];
    }

    // Disable create/edit/delete
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }
}
