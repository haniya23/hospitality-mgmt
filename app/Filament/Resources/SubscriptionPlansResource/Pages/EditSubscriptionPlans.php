<?php

namespace App\Filament\Resources\SubscriptionPlansResource\Pages;

use App\Filament\Resources\SubscriptionPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionPlans extends EditRecord
{
    protected static string $resource = SubscriptionPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete this subscription? This action cannot be undone and may affect the user\'s access to the platform.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load add-ons data
        $data['addons'] = $this->record->addons->map(function ($addon) {
            return [
                'id' => $addon->id,
                'qty' => $addon->qty,
                'unit_price_cents' => $addon->unit_price_cents,
                'cycle_start' => $addon->cycle_start,
                'cycle_end' => $addon->cycle_end,
            ];
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Log the changes
        $changes = [];
        $original = $this->record->getOriginal();
        
        foreach (['plan_slug', 'plan_name', 'status', 'base_accommodation_limit', 'price_cents'] as $field) {
            if (isset($data[$field]) && $data[$field] != $original[$field]) {
                $changes[$field] = [
                    'from' => $original[$field],
                    'to' => $data[$field],
                ];
            }
        }

        if (!empty($changes)) {
            activity()
                ->performedOn($this->record)
                ->causedBy(auth()->user())
                ->withProperties(['changes' => $changes])
                ->log('Subscription updated');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Handle add-ons updates
        if (isset($this->data['addons']) && is_array($this->data['addons'])) {
            // Get existing addon IDs
            $existingAddonIds = $this->record->addons->pluck('id')->toArray();
            $updatedAddonIds = [];

            foreach ($this->data['addons'] as $addonData) {
                if (isset($addonData['id']) && in_array($addonData['id'], $existingAddonIds)) {
                    // Update existing addon
                    $addon = $this->record->addons()->find($addonData['id']);
                    if ($addon) {
                        $addon->update($addonData);
                        $updatedAddonIds[] = $addonData['id'];
                    }
                } else {
                    // Create new addon
                    $newAddon = $this->record->addons()->create($addonData);
                    $updatedAddonIds[] = $newAddon->id;
                }
            }

            // Delete removed addons
            $addonsToDelete = array_diff($existingAddonIds, $updatedAddonIds);
            if (!empty($addonsToDelete)) {
                $this->record->addons()->whereIn('id', $addonsToDelete)->delete();
            }

            // Recalculate addon count
            $totalAddonQty = $this->record->addons()->sum('qty');
            $this->record->update(['addon_count' => $totalAddonQty]);
        }
    }
}
