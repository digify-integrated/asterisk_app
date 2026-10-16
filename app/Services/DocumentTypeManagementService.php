<?php

namespace App\Services;

use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;

class DocumentTypeManagementService
{
    public function saveDocumentType(array $data, ?int $userId): DocumentType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $documentType = DocumentType::query()->updateOrCreate(
                ['id' => $data['document_type_id'] ?? null],
                $payload
            );

            return $documentType;
        });
    }

    public function deleteDocumentType(int $documentTypeId): void
    {
        DB::transaction(function () use ($documentTypeId) {
            $documentType = DocumentType::query()->select(['id'])->findOrFail($documentTypeId);

            $documentType->delete();
        });
    }

    public function deleteMultipleDocumentTypes(array $documentTypeIds): void
    {
        DB::transaction(function () use ($documentTypeIds) {
            DocumentType::query()->whereIn('id', $documentTypeIds)->delete();
        });
    }
}