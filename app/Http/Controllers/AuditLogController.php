<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'invalid_session' => true,
                'message' => 'Your session has expired. Please log in again.',
                'redirect_link' => route('login'), 
            ]);
        }

        $tableName = $request->query('databaseTable');
        $referenceId = $request->query('referenceId');

        if (empty($tableName) || empty($referenceId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing database table or context reference identifier lookup arguments.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $logs = AuditLog::with('user')
                ->where('table_name', $tableName)
                ->where('reference_id', $referenceId)
                ->latest()
                ->get();

            $formattedLogs = $logs->map(function ($auditItem) {
                $user = $auditItem->user;

                $userName = $user ? $user->name : 'System/Unknown';
                $profilePic = $user && $user->profile_picture 
                    ? asset($user->profile_picture) 
                    : asset('assets/media/default/default-avatar.jpg');

                return [
                    'id'              => $auditItem->id,
                    'raw_log'         => $auditItem->log,
                    'user_name'       => $userName,
                    'profile_picture' => $profilePic,
                    'time_relative'   => $auditItem->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'logs'    => $formattedLogs,
            ]);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An internal database parsing error occurred while attempting to assemble logs.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}