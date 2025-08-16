<?php

namespace App\Filament\Resources\WorkOrderItems\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WorkOrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('work_order_id')
                    ->relationship('workOrder', 'ticket_no')
                    ->searchable()->preload()->required(),

                Select::make('item_type')
                    ->options(['service' => 'Service', 'part' => 'Part'])
                    ->required(),

                TextInput::make('item_id')->numeric()->required()->hint('ID from Service/Part'),
                TextInput::make('name_snapshot')->required(),
                TextInput::make('qty')->numeric()->default(1)->required(),
                TextInput::make('unit_price')->numeric()->required(),
                TextInput::make('line_total')->numeric()->required(),
                Toggle::make('installed'),

                Select::make('mechanic_id')
                    ->relationship('mechanic', 'name')
                    ->searchable()->preload()->label('Mechanic')->nullable(),
            ]);
    }
}
