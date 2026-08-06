<?php

namespace App\Filament\Resources\SyncJobs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SyncJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('database')
                    ->required(),
                Textarea::make('sql')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('interval')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('last_execute'),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
