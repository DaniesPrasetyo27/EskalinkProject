<?php

namespace App\Http\Controllers\api\Master;

use App\Http\Requests\PurchaseStatus\PurchaseReturnReleaseStatusBulkRequest;
use App\Http\Requests\PurchaseStatus\PurchaseReturnReleaseStatusRequest;
use App\Services\Master\PurchaseReturnReleaseStatusService;
use Illuminate\Http\Request;
use Eskalink\BeCoreBase\Http\Controllers\Controller;

class PurchaseReturnReleaseStatusController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PurchaseReturnReleaseStatusService();
    }
    public function index(Request $request)
    {
        return $this->service->index($request->all());
    }
    public function store(PurchaseReturnReleaseStatusRequest $request)
    {
        return $this->service->store($request->all());
    }
    public function show($id)
    {
        return $this->service->show($id);
    }

    public function update(PurchaseReturnReleaseStatusRequest $request, $id)
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

    public function bulkEdit(PurchaseReturnReleaseStatusBulkRequest $request)
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
