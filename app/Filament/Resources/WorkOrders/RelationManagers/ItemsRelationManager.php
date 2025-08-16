<?php

namespace App\Filament\Resources\WorkOrders\RelationManagers;

use Filament\Forms\Components;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Components\Select::make('item_type')
                ->options(['service' => 'Service', 'part' => 'Part'])
                ->required(),
            Components\TextInput::make('item_id')
                ->numeric()
                ->required(),
            Components\TextInput::make('name_snapshot')
                ->required(),
            Components\TextInput::make('qty')
                ->numeric()
                ->default(1)
                ->required(),
            Components\TextInput::make('unit_price')
                ->numeric()
                ->required(),
            Components\TextInput::make('line_total')
                ->numeric()
                ->required(),
            Components\Toggle::make('installed'),
            Components\Select::make('mechanic_id')
                ->relationship('mechanic', 'name')
                ->searchable()
                ->preload()
                ->label('Mechanic')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_type'),
                Tables\Columns\TextColumn::make('name_snapshot'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('unit_price'),
                Tables\Columns\TextColumn::make('line_total'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}