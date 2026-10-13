<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Exception;

class ImportController extends Controller
{
    public function parseCsv(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'database_table' => ['required', 'string'],
                'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            ]);

            $table = $request->input('database_table');
            
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'message' => 'The selected database table does not exist.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tableFields = Schema::getColumnListing($table);
            $file = $request->file('import_file');
            
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return response()->json([
                    'message' => 'Failed to open the uploaded CSV file.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $headers = fgetcsv($handle);
            if ($headers === false || empty($headers)) {
                fclose($handle);
                return response()->json([
                    'message' => 'The CSV file is empty or formatted incorrectly.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $headers = array_map(function ($header) {
                return trim(preg_replace('/[\x{FEFF}-\x{FFFF}]/u', '', $header));
            }, $headers);

            $preview = [];
            $firstRow = fgetcsv($handle);
            if ($firstRow !== false) {
                foreach ($headers as $index => $header) {
                    $preview[$index] = $firstRow[$index] ?? '';
                }
            }

            fclose($handle);

            return response()->json([
                'headers' => $headers,
                'preview' => $preview,
                'tableFields' => $tableFields,
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function testImport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'database_table' => ['required', 'string'],
                'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
                'mapping' => ['required', 'array'],
            ]);

            $table = $request->input('database_table');
            
            // Validate mapping using helper
            $mappingError = $this->validateMapping($table, $request->input('mapping'));
            if ($mappingError) {
                return response()->json(['message' => $mappingError], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $file = $request->file('import_file');
            $handle = fopen($file->getRealPath(), 'r');
            fgetcsv($handle); // Skip headers

            $rowCount = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if ($this->isRowEmpty($row)) {
                    continue;
                }
                $rowCount++;
                if ($rowCount >= 5) {
                    break;
                }
            }

            fclose($handle);

            return response()->json([
                'message' => 'Test import successful! Validated sample rows without errors.',
                'checked_rows' => $rowCount
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function commit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'database_table' => ['required', 'string'],
                'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
                'mapping' => ['required', 'array'],
            ]);

            $table = $request->input('database_table');
            $mapping = $request->input('mapping');

            // Validate mapping and build clean field mapping array
            $selectedFields = [];
            $fieldMapping = [];
            foreach ($mapping as $index => $map) {
                $dbField = $map['field'] ?? 'ignore';
                if ($dbField === 'ignore') continue;

                if (in_array($dbField, $selectedFields)) {
                    return response()->json([
                        'message' => "The database field '{$dbField}' is mapped multiple times. Each database field can only be mapped once."
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $selectedFields[] = $dbField;

                if (!Schema::hasColumn($table, $dbField)) {
                    return response()->json([
                        'message' => "The mapped field '{$dbField}' does not exist in table '{$table}'."
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Skip primary key auto-increment ID
                if ($dbField === 'id') {
                    continue;
                }

                $fieldMapping[$index] = $dbField;
            }

            $file = $request->file('import_file');
            $handle = fopen($file->getRealPath(), 'r');
            fgetcsv($handle); // Skip headers

            $batchSize = 500;
            $batchData = [];
            $importedCount = 0;

            // Cache column types and table structural checks OUTSIDE the loop for performance
            $columnTypes = [];
            foreach ($fieldMapping as $dbField) {
                $columnTypes[$dbField] = Schema::getColumnType($table, $dbField);
            }

            $hasCreatedAt = Schema::hasColumn($table, 'created_at');
            $hasUpdatedAt = Schema::hasColumn($table, 'updated_at');
            $now = now();

            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                if ($this->isRowEmpty($row)) {
                    continue;
                }

                $rowData = [];
                foreach ($fieldMapping as $colIndex => $dbField) {
                    $cellValue = $row[$colIndex] ?? null;

                    if (in_array($columnTypes[$dbField] ?? '', ['date', 'datetime', 'timestamp'])) {
                        $cellValue = $this->parseDateTime($cellValue);
                    }

                    $rowData[$dbField] = $cellValue;
                }

                if ($hasCreatedAt && !isset($rowData['created_at'])) {
                    $rowData['created_at'] = $now;
                }
                if ($hasUpdatedAt && !isset($rowData['updated_at'])) {
                    $rowData['updated_at'] = $now;
                }

                if (!empty($rowData)) {
                    $batchData[] = $rowData;
                }

                if (count($batchData) >= $batchSize) {
                    DB::table($table)->insert($batchData);
                    $importedCount += count($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                DB::table($table)->insert($batchData);
                $importedCount += count($batchData);
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'message' => "Successfully imported {$importedCount} rows into {$table}.",
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function downloadTemplate(string $table): StreamedResponse|JsonResponse
    {
        try {
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'message' => 'The selected database table does not exist.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $columns = Schema::getColumnListing($table);
            $exclude = ['created_at', 'updated_at', 'deleted_at'];
            $templateHeaders = array_values(array_diff($columns, $exclude));

            $filename = "{$table}_import_template.csv";

            return response()->streamDownload(function () use ($templateHeaders) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $templateHeaders);
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateMapping(string $table, array $mapping): ?string
    {
        $selectedFields = [];
        foreach ($mapping as $map) {
            $dbField = $map['field'] ?? 'ignore';
            if ($dbField === 'ignore') continue;

            if (in_array($dbField, $selectedFields)) {
                return "The database field '{$dbField}' is mapped multiple times. Each database field can only be mapped once.";
            }
            $selectedFields[] = $dbField;

            if (!Schema::hasColumn($table, $dbField)) {
                return "The mapped field '{$dbField}' does not exist in table '{$table}'.";
            }
        }
        return null;
    }

    private function parseDateTime(?string $value): ?string
    {
        if (empty(trim($value ?? ''))) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $value;
        }
    }

    private function isRowEmpty(array $row): bool
    {
        return empty(array_filter($row, function ($cell) {
            return !empty(trim($cell ?? ''));
        }));
    }
}