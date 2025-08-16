<?php

namespace App\Filament\Resources\WorkOrders\Tables;

use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Invoice;
use App\Models\InventoryMovement;
use App\Models\Part;

class WorkOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_no')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->sortable(),
                TextColumn::make('motorcycle.plate_no')
                    ->label('Motorcycle')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('mechanic.name')
                    ->label('Assigned Mechanic')
                    ->sortable()
                    ->default('Unassigned'),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Take Job')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'in_progress',
                            'assigned_mechanic_id' => auth()->id(),
                            'started_at' => now(),
                        ]);
                    }),

                Action::make('Mark Done')
                    ->visible(fn($record) => $record->status === 'in_progress')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'done', 'completed_at' => now()])),

                Action::make('Make Invoice')
                    ->visible(fn($record) => in_array($record->status, ['done', 'waiting_parts']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $lines = $record->items;
                        $total = $lines->sum('line_total');

                        $invoice = \App\Models\Invoice::create([
                            'work_order_id' => $record->id,
                            'customer_id' => $record->customer_id,
                            'motorcycle_id' => $record->motorcycle_id,
                            'number' => 'INV-' . now()->format('ymd-His'),
                            'tax' => 0,
                            'total' => $total,
                            'status' => 'paid', // MVP: mark paid in POS
                        ]);

                        foreach ($lines as $li) {
                            if ($li->item_type === 'part') {
                                InventoryMovement::create([
                                    'part_id' => $li->item_id,
                                    'change_qty' => -$li->qty,
                                    'reason' => 'sale',
                                    'ref_type' => 'invoice',
                                    'ref_id' => $invoice->id,
                                ]);
                                Part::whereKey($li->item_id)->decrement('stock_qty', $li->qty);
                            }
                        }

                        $record->update(['status' => 'invoiced']);
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
