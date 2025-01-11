<?php

namespace App\Http\Controllers\api\Master;


use App\Http\Requests\RequisitionFormula\SimulationFormulaRequest;
use App\Http\Requests\RequisitionFormula\StorePurchaseReqFormulaRequest;
use App\Http\Requests\RequisitionFormula\UpdatePurchaseReqFormulaRequest;
use App\Logs;
use App\Services\Master\PurchaseReqFormulaService;
use Eskalink\BeCoreBase\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PurchaseReqFormulaController extends Controller
{
    protected $reqFormulaService;

    function __construct(PurchaseReqFormulaService $reqFormulaService)
    {
        $this->reqFormulaService = $reqFormulaService;
        ini_set('memori_limit', '-1');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $parameters = isset($request['params']) ? (is_string($request['params']) ? json_decode($request['params']) : $request['params']) : array();
        // VALIDASI PARAMS
        if ($parameters && count($parameters) > 0) {
            $validator = Validator::make($request->all(), [
                'params' => 'required',
            ]);

            // Periksa apakah validasi parameter 'limit' berhasil
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            // Validasi payload secara kustom
            $payload = json_decode($request['params'], true) ?? [];

            $payloadErrors = [];
            foreach ($payload as $key => $criteria) {
                $validator = Validator::make($criteria, [
                    'field' => ['required'],
                    'operand' => ['required'],
                    'value' => [
                        'required',
                        'array',
                        function ($attribute, $value, $fail) use ($criteria) {
                            $name = $criteria['field'];
                            if ($name === 'code') {
                                foreach ($value as $val) {
                                    if ($val && !preg_match('/^[a-zA-Z0-9\,-_ ]+$/', $val)) {
                                        $fail("The Code must be alphanumeric and can only contain letters, numbers, underscores, and spaces.");
                                        return;
                                    }
                                }
                            }
                        },
                    ],
                ]);

                // Kumpulkan pesan kesalahan
                if ($validator->fails()) {
                    $payloadErrors[] = $validator->errors()->first();
                }
            }
            // Hitung jumlah kesalahan
            $totalErrors = count($payloadErrors);
            // Jika ada pesan kesalahan pada payload
            if (!empty($payloadErrors)) {
                $message = $payloadErrors[0];
                if ($totalErrors > 1) {
                    $message .= " (and " . ($totalErrors - 1) . " more errors)";
                }

                return response()->json(['message' => $message], 422);
            }
        }

        $logs = new Logs('User_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $results = $this->reqFormulaService->index($request->all());

            if ($results) {
                $results = [
                    'status'  => 200,
                    'success' => true,
                    'data'    => $results,
                ];

                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            }
        } catch (\Throwable $th) {
            Log::info($th);
            $logs->write(__FUNCTION__, $th->getMessage());
            $results = [
                'status'  => 500,
                'success' => false,
                'message' => 'Failed to retrieve data',
                'data'    => $th->getMessage(),
            ];
        }
        $logs->write(__FUNCTION__, "END\r\n");
        return response()->json($results, $results['status']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseReqFormulaRequest $request)
    {
        $logs = new Logs('User' . '_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $results = $this->reqFormulaService->store($request->validated());

            if ($results) {
                $results = [
                    'status'  => 201,
                    'success' => true,
                    'message' => 'Data has been saved.',
                    'data'    => $results,
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
                'message' => 'Data failed to save.',
                'data'    => $th->getMessage(),
            ];
        }

        $logs->write(__FUNCTION__, "END\r\n");

        return response()->json($results, $results['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseReqFormulaRequest $request, $reqFormula)
    {
        $logs = new Logs('User' . '_' . Arr::last(explode("\\", get_class())));
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();
            $results = $this->reqFormulaService->update($request->validated(), $reqFormula);

            if (count($results) > 0) {
                $results = [
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Data has been updated.',
                    'data'    => $results,
                ];

                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            } else {
                $results = [
                    'status'  => 200,
                    'success' => false,
                    'message' => 'No data has been edited.',
                ];
            }
        } catch (ModelNotFoundException $e) {
            $logs->write(__FUNCTION__, $e->getMessage());
            $results = [
                'status'  => 404,
                'success' => false,
                'message' => 'Data not found.',
            ];
        } catch (\Throwable $th) {
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $requisitionType)
    {
        $request = $request->validate([
            'deleted_by' => 'required|string|max:30',
        ]);
        $logs = new Logs($request['deleted_by']);
        $logs->write(__FUNCTION__, "START");

        try {
            DB::enableQueryLog();

            $results = $this->reqFormulaService->destroy($requisitionType, $request['deleted_by']);

            if ($results) {
                $results = [
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Data has been deleted.',
                ];

                $queries = DB::getQueryLog();
                for ($q = 0; $q < count($queries); $q++) {
                    $logs->write('BINDING', '[' . implode(', ', $queries[$q]['bindings']) . ']');
                    $logs->write('SQL', $queries[$q]['query']);
                }
            }
        } catch (ModelNotFoundException $e) {
            $logs->write(__FUNCTION__, $e->getMessage());
            $results = [
                'status'  => 404,
                'success' => false,
                'message' => 'Data not found.',
            ];
        } catch (\Throwable $th) {
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

    /**
     * Formula calculation simulation
     */
    public function simulation(SimulationFormulaRequest $reqFormula)
    {
        try {

            $results = $this->reqFormulaService->simulation($reqFormula->validated());

            if ($results) {
                $results = [
                    'status'  => 201,
                    'success' => true,
                    'message' => 'The simulation is successfully processed.',
                    'data'    => $results,
                ];
            }
        } catch (\Throwable $th) {
            $results = [
                'status'  => 500,
                'success' => false,
                'message'    => $th->getMessage(),
            ];
        }

        return response()->json($results, $results['status']);
    }
}
