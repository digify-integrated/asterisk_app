<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserOptionResource;
use App\Models\User;
use App\Http\Resources\UserTableResource;
use App\Http\Resources\UserDetailsResource;
use App\Http\Requests\SaveUserRequest;
use App\Http\Requests\FetchUserDetailsRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\DeleteMultipleUsersRequest;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class UserController extends Controller
{
    public function __construct(
        protected UserManagementService $userService
    ) {}

    public function save(SaveUserRequest $request): JsonResponse
    {
        try {
            $this->userService->saveUser(
                $request->validated(),
                $request->file('profile_picture'),
                Auth::id()
            );

            return response()->json([
                'message' => 'The user has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchUserDetailsRequest $request): JsonResponse|UserDetailsResource
    {
        try {
            $validated = $request->validated();

            $user = User::find($validated['user_id']);

            return new UserDetailsResource($user);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteUserRequest $request): JsonResponse
    {
        try {
            $this->userService->deleteUser((int) $request->validated()['user_id']);

            return response()->json([
                'message' => 'The user has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleUsersRequest $request): JsonResponse
    {
        try {
            $this->userService->deleteMultipleUsers($request->validated()['user_id']);

            return response()->json([
                'message' => 'The selected users have been deleted successfully',
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
        $defaultProfilePicture = asset('assets/media/default/default-avatar.jpg');

        $query = User::query();

        $query->when($request->filled('filter_status'), function ($q) use ($request) {
            $status = (array) $request->input('filter_status');
            $q->whereIn('status', $status);
        });

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $users = $query->orderBy('name')->get();

        return UserTableResource::collection($users)
            ->additional([
                'permissions'  => $permissions,
                'default_profile_picture' => $defaultProfilePicture,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $users = User::query()->orderBy('name')->get();

        return UserOptionResource::collection($users)
            ->response();
    }
}
