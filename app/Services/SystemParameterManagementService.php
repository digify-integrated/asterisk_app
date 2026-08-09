<?php

namespace App\Services;

use App\Models\SystemParameter;
use Illuminate\Support\Facades\DB;

class SystemParameterManagementService
{
    public function saveSystemParameter(array $data, ?int $userId): SystemParameter
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'value'         => $data['value'],
                'last_log_by'   => $userId,
            ];

            $systemParameter = SystemParameter::query()->updateOrCreate(
                ['id' => $data['system_parameter_id'] ?? null],
                $payload
            );

            return $systemParameter;
        });
    }

    public function deleteSystemParameter(int $systemParameterId): void
    {
        DB::transaction(function () use ($systemParameterId) {
            $systemParameter = SystemParameter::query()->select(['id'])->findOrFail($systemParameterId);

            $systemParameter->delete();
        });
    }

    public function deleteMultipleSystemParameters(array $systemParameterIds): void
    {
        DB::transaction(function () use ($systemParameterIds) {
            SystemParameter::query()->whereIn('id', $systemParameterIds)->delete();
        });
    }
}