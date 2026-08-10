<?php

namespace App\Filament\Resources\SyncJobs;

use App\Filament\Resources\SyncJobs\Pages\CreateSyncJob;
use App\Filament\Resources\SyncJobs\Pages\EditSyncJob;
use App\Filament\Resources\SyncJobs\Pages\ListSyncJobs;
use App\Filament\Resources\SyncJobs\RelationManagers;
use App\Jobs\ExecuteSyncJob;
use App\Models\SyncJob;
use BackedEnum;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SyncJobResource extends Resource
{
    protected static ?string $model = SyncJob::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

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

                TextColumn::make('interval')
                    ->suffix(' mnt'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state):string=>match($state){
                        'idle'=>'success',
                        'queued'=>'warning',
                        'running'=>'info',
                        'failed'=>'danger',
                        default=>'gray'
                    }),

                TextColumn::make('last_status')
                    ->badge()
                    ->color(fn(string $state):string=>match($state){
                        'idle'=>'success',
                        'queued'=>'warning',
                        'running'=>'info',
                        'failed'=>'danger',
                        default=>'gray'
                    }),

                TextColumn::make('last_execute')
                    ->since(),
                    
                // Convert last_duration from milliseconds to minutes and display with 2 decimal places
                TextColumn::make('last_duration')
                    ->formatStateUsing(fn($state) => $state ? number_format($state / 60000, 2) : '0.00')
                    ->label('Last Duration')
                    ->suffix(' mnt'),

                IconColumn::make('active')
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                // Penulisan Action menggunakan import yang benar dan type-hint Model
                Action::make('run')
                    ->label('Run')
                    ->icon('heroicon-o-play')
                    ->color(fn (SyncJob $record): string => static::isBusy($record) ? 'gray' : 'success')
                    ->disabled(fn (SyncJob $record): bool => static::isBusy($record))
                    ->requiresConfirmation()
                    ->modalHeading('Jalankan Sync Job')
                    ->modalDescription('Apakah Anda yakin ingin menjalankan job ini sekarang?')
                    ->action(function (SyncJob $record): void {
                        
                        // Eksekusi Job
                        ExecuteSyncJob::dispatch($record->id);
                        
                        // Update Status
                        $record->update([
                            'status' => 'queued'
                        ]);
                        
                        // Kirim notifikasi sukses ke User
                        Notification::make()
                            ->title('Job masuk antrean')
                            ->body("Sync Job '{$record->name}' berhasil dijalankan.")
                            ->success()
                            ->send();
                    })

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StepsRelationManager::class,
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

    protected static function isBusy(SyncJob $record): bool
    {
        return in_array($record->status, ['queued', 'running']);
    }
}