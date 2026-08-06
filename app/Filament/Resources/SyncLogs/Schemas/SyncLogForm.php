<?php

namespace App\Filament\Resources\SyncLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SyncLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sync_job_id')
                    ->required()
                    ->numeric(),
                Textarea::make('sql')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
