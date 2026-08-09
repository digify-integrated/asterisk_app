<?php

namespace App\Services;

use App\Models\SystemAction;
use Illuminate\Support\Facades\DB;

class SystemActionManagementService
{
    public function saveSystemAction(array $data, ?int $userId): SystemAction
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'last_log_by'   => $userId,
            ];

            $systemAction = SystemAction::query()->updateOrCreate(
                ['id' => $data['system_action_id'] ?? null],
                $payload
            );

            return $systemAction;
        });
    }

    public function deleteSystemAction(int $systemActionId): void
    {
        DB::transaction(function () use ($systemActionId) {
            $systemAction = SystemAction::query()->select(['id'])->findOrFail($systemActionId);

            $systemAction->delete();
        });
    }

    public function deleteMultipleSystemActions(array $systemActionIds): void
    {
        DB::transaction(function () use ($systemActionIds) {
            SystemAction::query()->whereIn('id', $systemActionIds)->delete();
        });
    }
}