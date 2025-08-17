<?php

namespace App\Filament\Resources\Motorcycles;

use App\Filament\Resources\Motorcycles\Pages\CreateMotorcycle;
use App\Filament\Resources\Motorcycles\Pages\EditMotorcycle;
use App\Filament\Resources\Motorcycles\Pages\ListMotorcycles;
use App\Filament\Resources\Motorcycles\Schemas\MotorcycleForm;
use App\Filament\Resources\Motorcycles\Tables\MotorcyclesTable;
use App\Models\Motorcycle;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MotorcycleResource extends Resource
{
    protected static ?string $model = Motorcycle::class;

    protected static UnitEnum|string|null $navigationGroup = 'POS';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admin users, hide from pos_user
        return auth()->check();
    }

    public static function form(Schema $schema): Schema
    {

        
        return MotorcycleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotorcyclesTable::configure($table);
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
            'index' => ListMotorcycles::route('/'),
            'create' => CreateMotorcycle::route('/create'),
            'edit' => EditMotorcycle::route('/{record}/edit'),
        ];
    }
}
