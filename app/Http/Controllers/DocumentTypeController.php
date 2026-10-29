<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveDocumentTypeRequest;
use App\Http\Requests\FetchDocumentTypeDetailsRequest;
use App\Http\Requests\DeleteDocumentTypeRequest;
use App\Http\Requests\DeleteMultipleDocumentTypesRequest;
use App\Services\DocumentTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class DocumentTypeController extends Controller
{
    public function __construct(
        protected DocumentTypeManagementService $documentTypeService
    ) {}

    public function save(SaveDocumentTypeRequest $request): JsonResponse
    {
        try {
            $this->documentTypeService->saveDocumentType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The document type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchDocumentTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $documentType = DocumentType::findOrFail($validated['document_type_id']);

            return new ConfigurationDetailsResource($documentType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteDocumentTypeRequest $request): JsonResponse
    {
        try {
            $this->documentTypeService->deleteDocumentType((int) $request->validated()['document_type_id']);

            return response()->json([
                'message' => 'The document type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleDocumentTypesRequest $request): JsonResponse
    {
        try {
            $this->documentTypeService->deleteMultipleDocumentTypes($request->validated()['document_type_id']);

            return response()->json([
                'message' => 'The selected document type have been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateTable(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('navigationMenuId');
        $user = $request->user();

        if (!$user || $menuId <= 0) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $permissions = $user->getMenuPermissions($menuId);

        $query = DocumentType::query();

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $documentTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($documentTypes)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $documentTypes = DocumentType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($documentTypes)
            ->response();
    }
}
