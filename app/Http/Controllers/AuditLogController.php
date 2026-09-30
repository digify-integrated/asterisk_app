<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Http\Resources\AuditLogCollectionResource;
use App\Http\Requests\FetchAuditLogDetailsRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class AuditLogController extends Controller
{
    public function fetch(FetchAuditLogDetailsRequest $request): JsonResponse|AuditLogCollectionResource
    {
        try {
            $validated = $request->validated();

            $logs = AuditLog::with('user')
                ->where('table_name', $validated['databaseTable'])
                ->where('reference_id', $validated['referenceId'])
                ->latest()
                ->get();

            return new AuditLogCollectionResource($logs);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}