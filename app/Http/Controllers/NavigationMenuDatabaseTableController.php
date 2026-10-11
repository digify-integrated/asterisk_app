<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatabaseTableImportOptionResource;
use App\Models\NavigationMenuDatabaseTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NavigationMenuDatabaseTableController extends Controller
{
    public function generateOption(Request $request): JsonResponse
    {
        $navigation_menu_id = $request->input('navigationMenuId');

        $navigationMenuDatabaseTables = NavigationMenuDatabaseTable::query()
            ->when($navigation_menu_id, function ($query, $id) {
                $query->where('navigation_menu_id', $id);
            })
            ->orderBy('database_table')
            ->get();

        return DatabaseTableImportOptionResource::collection($navigationMenuDatabaseTables)
            ->response();
    }
}