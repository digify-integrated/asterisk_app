<?php

namespace App\Services;

use App\Models\Relation;
use Illuminate\Support\Facades\DB;

class RelationManagementService
{
    public function saveRelation(array $data, ?int $userId): Relation
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $relation = Relation::query()->updateOrCreate(
                ['id' => $data['relation_id'] ?? null],
                $payload
            );

            return $relation;
        });
    }

    public function deleteRelation(int $relationId): void
    {
        DB::transaction(function () use ($relationId) {
            $relation = Relation::query()->select(['id'])->findOrFail($relationId);

            $relation->delete();
        });
    }

    public function deleteMultipleRelations(array $relationIds): void
    {
        DB::transaction(function () use ($relationIds) {
            Relation::query()->whereIn('id', $relationIds)->delete();
        });
    }
}