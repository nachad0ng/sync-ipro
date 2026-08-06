<?php

namespace App\Filament\Resources\SyncJobs;

use App\Filament\Resources\SyncJobs\Pages\CreateSyncJob;
use App\Filament\Resources\SyncJobs\Pages\EditSyncJob;
use App\Filament\Resources\SyncJobs\Pages\ListSyncJobs;
use App\Models\SyncJob;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SyncJobResource extends Resource
{
    protected static ?string $model = SyncJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('database')
                    ->required(),

                Forms\Components\Textarea::make('sql')
                    ->rows(10)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('interval')
                    ->numeric()
                    ->default(30)
                    ->suffix('menit'),

                Forms\Components\TextInput::make('timeout')
                    ->numeric()
                    ->default(300)
                    ->suffix('detik'),

                Forms\Components\TextInput::make('retry')
                    ->numeric()
                    ->default(3),

                Forms\Components\Toggle::make('active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('database'),

                IconColumn::make('active')
                    ->boolean(),

                TextColumn::make('interval')
                    ->suffix(' mnt'),

                TextColumn::make('last_execute')
                    ->since(),

                TextColumn::make('updated_at')
                    ->since(),
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
            'index' => ListSyncJobs::route('/'),
            'create' => CreateSyncJob::route('/create'),
            'edit' => EditSyncJob::route('/{record}/edit'),
        ];
    }
}