<?php

namespace App\Http\Controllers\api\Master;

use Illuminate\Http\Request;
use App\Services\Master\PurchaseOrdRejStatusService;
use Eskalink\BeCoreBase\Http\Controllers\Controller;
use App\Http\Requests\Master\PurchaseOrdRejStatus\PurchaseOrdRejStatusRequest;
use App\Http\Requests\Master\PurchaseOrdRejStatus\PurchaseOrdRejStatusBulkRequest;

class PurchaseOrdRejStatusController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PurchaseOrdRejStatusService();
    }
    public function index(Request $request)
    {
        return $this->service->index($request->all());
    }
    public function store(PurchaseOrdRejStatusRequest $request)
    {
        return $this->service->store($request->all());
    }
    public function show($id)
    {
        return $this->service->show($id);
    }

    public function update(PurchaseOrdRejStatusRequest $request, $id)
    {
        return $this->service->update($request->all(), $id);
    }

    public function destroy($id, Request $request)
    {
        $request->validate([
            'deleted_by' => ['required', 'string', 'max:30'],
        ]);
        return $this->service->destroy($id, $request->all());
    }

    public function bulkEdit(PurchaseOrdRejStatusBulkRequest $request)
    {
        return $this->service->bulkEdit($request->validated());
    }

    public function printIndex(Request $request)
    {
        return $this->service->printIndex($request->all());
    }

    public function importCsv(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*' => 'required|array',
        ]);
        return $this->service->importCsv($validated['data']);
    }
}
