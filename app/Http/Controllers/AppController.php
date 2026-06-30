<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    public function generateTable(Request $request)
    {
        // 1. Base Query Initialization
        $query = DB::table('apps');

        // ---- IF SERVERSIDE IS TRUE: Handle Pagination & Filtering ----
        if ($request->has('draw')) {
            $totalRecords = $query->count();

            // Handle Custom Global Search Input (#datatable-search)
            if ($searchValue = $request->input('search.value')) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('description', 'like', "%{$searchValue}%");
                });
            }

            $filteredRecords = $query->count();

            // Handle Dynamic Length Control Selectors (#datatable-length)
            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 25));

            // Apply DB chunk slicing
            // Note: If -1 (All) bypassed our JS protection, handle it via a massive fallback ceiling
            if ($length === -1) {
                $apps = $query->orderBy('name')->get();
            } else {
                $apps = $query->orderBy('name')
                              ->skip($start)
                              ->take($length)
                              ->get();
            }

            // Map out HTML payload data structured blocks
            $data = $this->mapTableRows($apps);

            // Return structural metadata payload wrapper
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $data
            ]);
        }

        // ---- IF SERVERSIDE IS FALSE: Dump Everything Once ----
        $apps = $query->orderBy('name')->get();
        return response()->json($this->mapTableRows($apps));
    }

    /**
     * Helper to keep HTML row formatting unified across both processing modes.
     */
    private function mapTableRows($collection)
    {
        return $collection->map(function ($row) {
            $appId = $row->id;
            $name = $row->name;
            $description = $row->description;
            
            $defaultLogo = asset('assets/media/default/app-logo.png');
            $path = trim((string) ($row->logo ?? ''));

            $logoUrl = $path !== '' && Storage::disk('public')->exists($path)
                ? Storage::url($path)
                : $defaultLogo;

            return [
                'CHECK_BOX' => '
                    <div class="form-check form-check-sm ms-5">
                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="' . $appId . '">
                    </div>',
                'APP' => '
                    <div class="d-flex align-items-center">
                        <img src="'.$logoUrl.'" alt="app-logo" width="45" />
                        <div class="ms-3">
                            <div class="user-meta-info">
                                <h6 class="mb-0">'.e($name).'</h6>
                                <small class="text-wrap fs-7 text-gray-500">'.e($description).'</small>
                            </div>
                        </div>
                    </div>
                ',
                'ACTION' => ''
            ];
        })->values()->all();
    }
}
