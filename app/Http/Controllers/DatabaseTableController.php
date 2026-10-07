<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatabaseTableOptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DatabaseTableController extends Controller
{
    public function generateOption(Request $request): JsonResponse
    {
        $databaseName = Config::get('database.connections.' . config('database.default') . '.database');

        $databaseTables = Schema::getTableListing($databaseName);

        return DatabaseTableOptionResource::collection($databaseTables)
            ->response();
    }
}
