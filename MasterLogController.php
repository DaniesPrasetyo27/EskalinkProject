<?php

namespace App\Http\Controllers\api\Master;

use App\Services\logs\MasterLogService;
use Eskalink\BeCoreBase\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterLogController extends Controller
{
    protected $masterLogService;

    function __construct(MasterLogService $masterLogService)
    {
        $this->masterLogService = $masterLogService;
    }

    /**
     * Display the specified resource.
     */

    public function show($recordId)
    {
        $orderBy = request()->get('order', 'desc');

        return response()->json($this->masterLogService->show($recordId, $orderBy));
    }

    /**
     * Display multitable and record
     */
    public function multiTableIndex(Request $request)
    {
        try {
            $masterLogsResult = $this->masterLogService->multiTableIndex($request->all());

            if (!$masterLogsResult) {
                return response()->json([
                    'message' => 'Logs not found.',
                    'data' => $masterLogsResult
                ], 404);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'message' => 'Failed to retrieve logs.'
            ], 500);
        }

        return response()->json($masterLogsResult, 200);
    }
}
