<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('number')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'paid' => 'Paid',
                                'pending' => 'Pending',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('paid'),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('motorcycle_id')
                            ->label('Motorcycle')
                            ->relationship('motorcycle', 'plate_no')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Select::make('work_order_id')
                    ->label('Work Order (Optional)')
                    ->relationship('workOrder', 'ticket_no')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('tax')
                            ->label('GST Amount')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
