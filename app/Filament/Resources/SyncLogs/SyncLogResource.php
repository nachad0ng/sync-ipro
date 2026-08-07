<?php

namespace App\Filament\Resources\SyncLogs;

use App\Filament\Resources\SyncLogs\Pages\ListSyncLogs;
use App\Models\SyncLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SyncLogResource extends Resource
{
    protected static ?string $model = SyncLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Boleh dikosongkan karena log tidak bisa di-edit/create
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job.name')
                    ->label('Job'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'success',
                        'failed' => 'danger',
                    ]),

                TextColumn::make('duration')
                    ->suffix(' ms'),

                TextColumn::make('message')
                    ->limit(50),

                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => ListSyncLogs::route('/'),
            // Hapus route create dan edit
        ];
    }

    // Dynamic Permission Checks: Mencegah tombol Create dan Edit muncul
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}