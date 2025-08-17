<?php

namespace App\Filament\Resources\WorkOrderItems;

use App\Filament\Resources\WorkOrderItems\Pages\CreateWorkOrderItem;
use App\Filament\Resources\WorkOrderItems\Pages\EditWorkOrderItem;
use App\Filament\Resources\WorkOrderItems\Pages\ListWorkOrderItems;
use App\Filament\Resources\WorkOrderItems\Schemas\WorkOrderItemForm;
use App\Filament\Resources\WorkOrderItems\Tables\WorkOrderItemsTable;
use App\Models\WorkOrderItem;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkOrderItemResource extends Resource
{
    protected static ?string $model = WorkOrderItem::class;

    protected static UnitEnum|string|null $navigationGroup = 'POS';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admin users, hide from pos_user
        return auth()->check();
    }

    public static function form(Schema $schema): Schema
    {
        return WorkOrderItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkOrderItemsTable::configure($table);
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
            'index' => ListWorkOrderItems::route('/'),
            'create' => CreateWorkOrderItem::route('/create'),
            'edit' => EditWorkOrderItem::route('/{record}/edit'),
        ];
    }
}
