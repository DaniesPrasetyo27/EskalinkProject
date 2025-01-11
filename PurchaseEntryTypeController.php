<?php

namespace App\Http\Controllers\api\Master;

use App\Logs;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Master\DiscountLevelService;
use App\Http\Requests\PurchaseEntryType\PurchaseEntryTypeRequest;
use App\Http\Requests\PurchaseEntryType\PurchaseEntryTypeBulkRequest;
use App\Services\Master\PurchaseEntryTypeService;
use Eskalink\BeCoreBase\Http\Controllers\Controller;

class PurchaseEntryTypeController extends Controller
{
    protected $DiscountLevelService;

    function __construct()
    {
        $this->DiscountLevelService = new PurchaseEntryTypeService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $result = $this->DiscountLevelService->index($request->all());
            if (!$result) {
                return response()->json([
                    'message' => 'Data not found',
                    'data' => $result
                ], 404);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $result
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseEntryTypeRequest $request)
    {
        $results['status']  = 201;
        $results['success'] = true;
        $results['message'] = '';
        $logs = new Logs('User_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $result = $this->DiscountLevelService->store($request->all());

            if ($result) {
                $results['message'] = 'Successfully create data';
                $results['data'] = $result;
                $queries = DB::getQueryLog();

                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            }
        } catch (\Throwable $th) {
            $logs->write("ERROR", $th->getMessage());

            $results = [
                'status'  => 500,
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        $logs->write(__FUNCTION__, "STOP\r\n");

        return response()->json(
            $results,
            $results['status']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $result = $this->DiscountLevelService->show($id);

            if (!$result) {
                return response()->json([
                    'message' => 'Data not found.',
                    'data' => $result
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully retrieved data',
            'data' => $result
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseEntryTypeRequest $request, $id)
    {
        $results['status']  = 200;
        $results['success'] = true;
        $results['message'] = '';
        $logs = new Logs('User_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();
            $result = $this->DiscountLevelService->update($request->all(), $id);

            if ($result === 304) {
                $results['message'] = 'No data has been edited.';
            } else if ($result) {
                $results['message'] = 'Data successfully updated.';
                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            }
        } catch (\Throwable $th) {
            $logs->write("ERROR", $th->getMessage());
            Log::info($th);
            $results = [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to update data.'
            ];
        }

        $logs->write(__FUNCTION__, "STOP\r\n");

        return response()->json($results, $results['status']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $results['status']  = 200;
        $results['success'] = true;
        $results['message'] = 'Data has been deleted!';

        try {
            $result = $this->DiscountLevelService->destroy($id, $request->all());
        } catch (\Throwable $th) {
            Log::error($th);
            $results = [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to delete data.'
            ];
        }

        return response()->json($results, $results['status']);
    }

    public function bulkEdit(PurchaseEntryTypeBulkRequest $request)
    {
        $logs = new Logs('User' . '_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $results = $this->DiscountLevelService->bulkEdit($request->validated());

            if ($results > 0) {
                $results = [
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Data has been updated.',
                    'data'    => count($results) . ' data edited.',
                ];

                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            } else {
                $results = [
                    'status'  => 304,
                    'success' => false,
                    'message' => 'No data edited.',
                ];
            }
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            $logs->write(__FUNCTION__, $e->getMessage());
            $results = [
                'status'  => 404,
                'success' => false,
                'message' => 'Data not found.',
            ];
        } catch (\Throwable $th) {
            Log::error($th);
            $logs->write(__FUNCTION__, $th->getMessage());
            $results = [
                'status'  => 500,
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        $logs->write(__FUNCTION__, "END\r\n");

        return response()->json($results, $results['status']);
    }

    public function printIndex(Request $request)
    {
        try {
            $results = $this->DiscountLevelService->printIndex($request->all());

            if (!$results) {
                return response()->json([
                    'message' => 'Data not found.',
                    'data' => $results
                ], 404);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'message' => 'Failed to retrieve data.'
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully retrieved data.',
            'data' => $results
        ], 200);
    }

    function importCsv(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*' => 'required|array',
        ]);

        $logs = new Logs('User' . '_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $results = $this->DiscountLevelService->importCsv($validated['data']);

            if ($results) {
                $results = [
                    'status'  => 201,
                    'success' => true,
                    'message' => count($validated['data']) . ' data has been imported!',
                ];

                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            }
        } catch (\Throwable $th) {
            $logs->write(__FUNCTION__, $th->getMessage());
            $results = [
                'status'  => 500,
                'success' => false,
                'message' => 'Fail to import data.',
                'data'    => $th->getMessage(),
            ];
        }

        $logs->write(__FUNCTION__, "END\r\n");

        return response()->json($results, $results['status']);
    }
}
