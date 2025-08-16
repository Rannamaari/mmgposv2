<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load current role for editing
        $data['role'] = $this->record->getRoleNames()->first();
        return $data;
    }

    protected function afterSave(): void
    {
        // Update role after saving
        $role = $this->data['role'] ?? 'pos_user';
        $this->record->syncRoles([$role]);
    }
}
