<?php

namespace App\Filament\Resources\WorkOrders\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WorkOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()->preload()->required(),

                Select::make('motorcycle_id')
                    ->relationship('motorcycle', 'plate_no')
                    ->searchable()->preload()->required()
                    ->hint('Filter by plate number'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'waiting_parts' => 'Waiting for Parts',
                        'done' => 'Done',
                        'invoiced' => 'Invoiced',
                        'canceled' => 'Canceled',
                    ])
                    ->default('pending')
                    ->required(),
                Select::make('assigned_mechanic_id')
                    ->label('Assigned Mechanic')
                    ->relationship('mechanic', 'name')
                    ->searchable()->preload()
                    ->nullable(),
                Textarea::make('notes')->rows(3),
            ]);
    }
}
