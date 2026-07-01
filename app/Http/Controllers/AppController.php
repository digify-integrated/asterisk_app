<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    public function generateTable(Request $request)
    {
        $pageNavigationMenuId = (int) $request->input('navigationMenuId');
        $user = $request->user();

        if (!$user || $pageNavigationMenuId <= 0) {
            return response()->json(['error' => 'Unauthorized or missing menu parameter.'], 403);
        }

        $permissions = $user->getMenuPermissions($pageNavigationMenuId);

        $apps = DB::table('apps')->orderBy('name')->get();
        $defaultLogo = asset('assets/media/default/app-logo.png');

        $response = $apps->map(function ($row) use ($permissions, $defaultLogo) {
            $appId = $row->id;            
            $path = trim((string) ($row->logo ?? ''));
            $logoUrl = ($path !== '' && Storage::disk('public')->exists($path)) ? Storage::url($path) : $defaultLogo;

            return [
                'CHECK_BOX' => '
                    <div class="form-check form-check-sm ms-5">
                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="' . $appId . '">
                    </div>',
                'APP' => '
                    <div class="d-flex align-items-center">
                        <img src="'. $logoUrl .'" alt="app-logo" width="45" />
                        <div class="ms-3">
                            <div class="user-meta-info">
                                <h6 class="mb-0">'. e($row->name) .'</h6>
                                <small class="text-wrap fs-7 text-gray-500">'. e($row->description) .'</small>
                            </div>
                        </div>
                    </div>',
                'ACTION' => sprintf(
                    '<div class="d-flex justify-content-end gap-2 me-5">%s %s %s</div>',
                    $permissions['write'] ? '<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="#form-modal" data-reference-id="' . $appId . '" title="Update App"><i class="ki-outline ki-eye fs-5 m-0"></i></button>' : '',
                    $permissions['logs'] ? '<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="' . $appId . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View System Audit Trail"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>' : '',
                    $permissions['delete'] ? '<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="' . $appId . '" title="Delete App"><i class="ki-outline ki-trash fs-5 m-0"></i></button>' : ''
                ),
            ];
        })->values();

        return response()->json($response);
    }
}
