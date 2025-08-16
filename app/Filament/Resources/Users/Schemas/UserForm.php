<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrateStateUsing(
                        fn(?string $state): ?string =>
                        filled($state) ? Hash::make($state) : null
                    )
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->placeholder(
                        fn(string $context): string =>
                        $context === 'edit' ? 'Leave blank to keep current password' : 'Enter password'
                    ),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->same('password')
                    ->dehydrated(false),

                Select::make('roles')
                    ->label('User Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->helperText('Admin: Full access to all features. POS User: Limited access to POS, Invoices, Parts, and Services only.'),

                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->helperText('Set this to mark the email as verified'),
            ]);
    }
}
