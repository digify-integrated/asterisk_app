<?php

namespace App\Services;

use App\Models\State;
use Illuminate\Support\Facades\DB;

class StateManagementService
{
    public function saveState(array $data, ?int $userId): State
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'country_id'    => $data['country_id'],
                'last_log_by'   => $userId,
            ];

            $states = State::query()->updateOrCreate(
                ['id' => $data['state_id'] ?? null],
                $payload
            );

            return $states;
        });
    }

    public function deleteState(int $stateId): void
    {
        DB::transaction(function () use ($stateId) {
            $state = State::query()->select(['id'])->findOrFail($stateId);

            $state->delete();
        });
    }

    public function deleteMultipleStates(array $stateIds): void
    {
        DB::transaction(function () use ($stateIds) {
            State::query()->whereIn('id', $stateIds)->delete();
        });
    }
}