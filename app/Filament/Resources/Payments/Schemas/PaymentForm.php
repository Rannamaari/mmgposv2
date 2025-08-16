<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->relationship('invoice', 'number')
                    ->searchable()->preload()->required(),

                Select::make('method')
                    ->options(['cash' => 'Cash', 'bml_transfer' => 'BML Transfer'])
                    ->required(),

                Forms\Components\TextInput::make('amount')->numeric()->required(),

                FileUpload::make('proof_image_path')
                    ->directory('proofs')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048), // we'll still compress server-side in POS page
                Select::make('received_by')->relationship('receivedBy', 'name')->searchable()->preload()->required(),
                DateTimePicker::make('received_at')->required(),

            ]);
    }
}
