<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('default_price')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('category'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_quick_service')
                    ->label('Add to Quick Services')
                    ->helperText('This service will appear as a quick service button in POS')
                    ->default(false),
            ]);
    }
}
