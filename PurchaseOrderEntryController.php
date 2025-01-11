<?php

namespace App\Http\Controllers\api\Transaction\PurchaseOrder;

use App\Http\Requests\Transaction\PurchaseOrder\PurchaseOrderEntryRequest;
use App\Services\Transaction\PurchaseOrder\PurchaseOrderEntryService;
use Illuminate\Http\Request;
use Eskalink\BeCoreBase\Http\Controllers\Controller;

class PurchaseOrderEntryController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PurchaseOrderEntryService();
    }
    public function index(Request $request)
    {
        return $this->service->index($request->all());
    }
    public function store(Request $request)
    {
        return $this->service->store($request->all());
    }
    public function show($id)
    {
        return $this->service->show($id);
    }

    public function update(PurchaseOrderEntryRequest $request, $id)
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
