<?php

namespace App\Filament\Resources\SyncJobs\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    protected static ?string $title = 'Steps';
    
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name') 
            ->defaultSort('step_no')
            ->columns([
                Tables\Columns\TextColumn::make('step_no')
                    ->label('Step'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['step_no'] = ($this->ownerRecord->steps()->max('step_no') ?? 0) + 1;

                        return $data;
                    })
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama yang tampil di log.'),
                        Forms\Components\Textarea::make('sql')
                            ->rows(12)
                            ->columnSpanFull()
                            ->extraInputAttributes([
                                'style' => 'font-family: monospace'
                            ])
                            ->required(),
                    ])
            ])
            ->actions([
                EditAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama yang tampil di log.'),
                        Forms\Components\Textarea::make('sql')
                            ->rows(12)
                            ->columnSpanFull()
                            ->extraInputAttributes([
                                'style' => 'font-family: monospace'
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('active')
                            ->default(true),
                    ]),
                DeleteAction::make(),
            ]);
    }
}