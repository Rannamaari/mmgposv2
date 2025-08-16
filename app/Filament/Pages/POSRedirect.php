<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;

class POSRedirect extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = '🏪 POS System';
    protected static UnitEnum|string|null $navigationGroup = 'Quick Access';
    protected static ?int $navigationSort = 1;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        // Directly return the POS URL instead of showing a page
        return '/pos';
    }
}