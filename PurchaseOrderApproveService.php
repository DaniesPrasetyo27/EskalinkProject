<?php

namespace App\Services\Transaction\PurchaseOrder;

use App\Models\Master\TransferType;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderBillEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderEntryStatus;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductEntryShip;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductSumEntry;
use Eskalink\BeCoreBase\Services\Core\BaseService;
use Eskalink\BeCoreBase\Repositories\Core\BaseRepository;
use Eskalink\BeCoreBase\Repositories\Logs\MasterLogRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderApproveService extends BaseService
{
    protected $baseRepository;
    protected $baseStatus;
    protected $statusRepository;
    protected $MasterLogRepository;

    private $config = [
        "tableFields" => [
            'doc_no' => [
                "isRemap" => false
            ],
            'doc_date' => [
                "isRemap" => false
            ],
            'req_delv_date' => [
                "isRemap" => false
            ],
            'req_del_note' => [
                "isRemap" => false
            ],
            'is_active' => [
                "isRemap" => false,
            ],
        ]
    ];

    public function __construct()
    {
        $this->baseStatus = new BaseRepository(new PurchaseOrderEntryStatus(), $this->config);
        $this->baseRepository = new BaseRepository(new PurchaseOrderEntry(), $this->config);
        $this->MasterLogRepository = new MasterLogRepository();
    }

    public function index($request)
    {
        return $this->executeIndex(function () use ($request) {
            $data = [];
            $headers = $this->baseRepository->index($request, function($query) use ($request) {
                $transferStatus = PurchaseOrderEntryStatus::whereIn("doc_sts", ["30", "31", "32"])
                    ->distinct(["puransordentsts.parent_doc_id"]);

                $query->join("transferstatus", "transferstatus.parent_doc_id", "=", "puransordentdoc.id");

                return $query->withExpression("transferstatus", $transferStatus)
                                ->where("puransordentdoc.is_active", "=", 1)
                                ->select("puransordentdoc.*");
            });
            
            foreach ($headers as $key => $value) {
                // CALL ENTRY SHIP
                $transferShip = new PurchaseOrderProductEntryShip();
                $headers[$key]['puransordentship'] = $transferShip->where("parent_doc_id", "=", $headers[$key]["id"])->get();

                // CALL BILL ENTRY
                $transferBill = new PurchaseOrderBillEntry();
                $headers[$key]['puransordentbill'] = $transferBill->where("parent_doc_id", "=", $headers[$key]["id"])->get();

                // CALL DETAIL ENTRY
                $transferStatus = new PurchaseOrderEntryStatus();
                $headers[$key]['puransordentsts'] = $transferStatus->where("parent_doc_id", "=", $headers[$key]['id'])->first();

                // CALL PRODUCT ENTRY SUM
                $transferProductSum = new PurchaseOrderProductSumEntry();
                $headers[$key]['puransordentprdsum'] = $transferProductSum->where("parent_doc_id", "=", $headers[$key]["id"])->get();
    
                if (!is_null($headers[$key]['puransordentprdsum'])) {
                    foreach ($headers[$key]['puransordentprdsum'] as $innerKey => $innerValue) {
                        // CALL PRODUCT INSIDE PRODUCT SUMMARY
                        $data = PurchaseOrderProductEntry::where("parent_doc_id", $headers[$key]['id'])
                                    ->where("prd_id", $innerValue["prd_id"])->get();
                        $headers[$key]["puransordentprdsum"][$innerKey]["puransordentprd"] = $data;
                    }
                }
            }
            return $headers;
        });
    }
    
    
    public function store($request)
    {
        return $this->executeStore(function () use ($request) {
            DB::transaction(function () use ($request) {
                try {
                    $dataPurchaseEntry = isset($request['puransordentdoc']) ? $request['puransordentdoc'] : null;
                    $dataPurchaseStatus = isset($request['puransordentsts']) ? $request['puransordentsts'] : null;
                    $dataPurchaseShip = isset($request['puransordentship']) ? $request['puransordentship'] : null;
                    $dataBillEntry = isset($request['puransordentbill']) ? $request['puransordentbill'] : null;
                    $dataProductSum = isset($request['puransordentprdsum']) ? $request['puransordentprdsum'] : [];

                    if (isset($dataPurchaseEntry)) {
                        $trfEntry = PurchaseOrderEntry::create($dataPurchaseEntry);
                        $parentDocId = $trfEntry->id;

                        if (isset($dataPurchaseStatus)) {
                            $dataPurchaseStatus['parent_doc_id'] = $parentDocId;
                            PurchaseOrderEntryStatus::create($dataPurchaseStatus);
                        }

                        if (isset($dataBillEntry)) {
                            
                            $dataBillEntry['parent_doc_id'] = $parentDocId;
                            PurchaseOrderBillEntry::create($dataBillEntry);
                        }

                        if (isset($dataPurchaseShip)) {
                            
                            $dataPurchaseShip['parent_doc_id'] = $parentDocId;
                            PurchaseOrderProductEntryShip::create($dataPurchaseShip);
                        }

                        foreach ($dataProductSum as $paramPrdSum) {
                            $paramPrdSum['parent_doc_id'] = $parentDocId;
                            $paramPrdSum['prd_id'] = $paramPrdSum['id'];
                            $paramPrdSum['prd_code'] = $paramPrdSum['code'];
                            $paramPrdSum['prd_shortdesc'] = $paramPrdSum['shortdesc'];
                            $paramPrdSum['prd_fulldesc'] = $paramPrdSum['fulldesc'];
                            $paramPrdSum['created_by'] = $paramPrdSum['created_by'] ?? 'system';
                            PurchaseOrderProductSumEntry::create($paramPrdSum);
                        }

                        foreach ($dataProductSum as $paramPrd) {
                            foreach ($paramPrd['puransordentprd'] as &$pkgData) {
                                // print_r($paramPrd['proppiunipkg']);
                                // exit(); 
                                $pkgData['parent_doc_id'] = $parentDocId;
                                $pkgData['prd_id'] = $paramPrd['id'];
                                $pkgData['prd_code'] = $paramPrd['code'];
                                $pkgData['prd_shortdesc'] = $paramPrd['shortdesc'];
                                $pkgData['prd_seq'] = $paramPrd['prd_seq'] ?? 1;
                                $pkgData['created_by'] = $paramPrd['created_by'] ?? 'system';
                                $pkgData['pkg_seq'] = $pkgData['unitpkg_seq'];
                                $pkgData['qty'] = $pkgData['qty'] ?? 0;
                                $pkgData['baseqty'] = $paramPrd['baseqty'] ?? 0;
                                $pkgData['convpkg'] = $paramPrd['convpkg'] ?? 0;

                                PurchaseOrderProductEntry::create($pkgData);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY STORE METHOD"]);
                    throw new \Exception($e->getMessage());
                }
            });
        });
    }

    public function show($id)
    {
        return $this->executeShow(function () use ($id) {
            $transferEntry["puransordentdoc"] = $this->baseRepository->show($id);

            if($transferEntry != null) {
                $transferEntry["puransordentsts"] = PurchaseOrderEntryStatus::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentbill"] = PurchaseOrderBillEntry::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentship"] = PurchaseOrderProductEntryShip::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentprdsum"] = PurchaseOrderProductSumEntry::where("parent_doc_id", $id)->get();
                
                foreach($transferEntry["puransordentprdsum"] as $key => $value) {
                    $transferEntry["puransordentdoc"]["doc_date"] = '2024-03-18';
                    $data = PurchaseOrderProductEntry::where("parent_doc_id", $id)
                                ->where("prd_id", $transferEntry["puransordentprdsum"][$key]["prd_id"])->get();
                    $transferEntry["puransordentprdsum"][$key]["puransordentprd"] = $data;
                }
            }
            return $transferEntry;
        });
    }

    public function update($request, $id)
    {
        return $this->executeUpdate(function () use ($request) {
            DB::transaction(function () use ($request) {
                try {
                    $dataPurchaseEntry = isset($request['puransordentdoc']) ? $request['puransordentdoc'] : null;
                    $dataPurchaseStatus = isset($request['puransordentsts']) ? $request['puransordentsts'] : null;
                    $dataPurchaseShip = isset($request['puransordentship']) ? $request['puransordentship'] : null;
                    $dataBillEntry = isset($request['puransordentbill']) ? $request['puransordentbill'] : null;
                    $dataProductSum = isset($request['puransordentprdsum']) ? $request['puransordentprdsum'] : [];

                    if (isset($request)) {
                        $trfEntry = PurchaseOrderEntry::create($dataPurchaseEntry);
                        $parentDocId = $trfEntry->id;

                        if (isset($dataPurchaseStatus)) {
                            $dataPurchaseStatus['parent_doc_id'] = $parentDocId;
                            PurchaseOrderEntryStatus::create($dataPurchaseStatus);
                        }

                        if (isset($dataBillEntry)) {
                            
                            $dataBillEntry['parent_doc_id'] = $parentDocId;
                            PurchaseOrderBillEntry::create($dataBillEntry);
                        }

                        if (isset($dataPurchaseShip)) {
                            
                            $dataPurchaseShip['parent_doc_id'] = $parentDocId;
                            PurchaseOrderProductEntryShip::create($dataPurchaseShip);
                        }

                        foreach ($dataProductSum as $paramPrdSum) {
                            $paramPrdSum['parent_doc_id'] = $parentDocId;
                            $paramPrdSum['prd_id'] = $paramPrdSum['id'];
                            $paramPrdSum['prd_code'] = $paramPrdSum['code'];
                            $paramPrdSum['prd_shortdesc'] = $paramPrdSum['shortdesc'];
                            $paramPrdSum['prd_fulldesc'] = $paramPrdSum['fulldesc'];
                            $paramPrdSum['created_by'] = $paramPrdSum['created_by'] ?? 'system';
                            PurchaseOrderProductSumEntry::create($paramPrdSum);
                        }

                        foreach ($dataProductSum as $paramPrd) {
                            foreach ($paramPrd['puransordentprd'] as &$pkgData) {
                                // print_r($paramPrd['proppiunipkg']);
                                // exit(); 
                                $pkgData['parent_doc_id'] = $parentDocId;
                                $pkgData['prd_id'] = $paramPrd['id'];
                                $pkgData['prd_code'] = $paramPrd['code'];
                                $pkgData['prd_shortdesc'] = $paramPrd['shortdesc'];
                                $pkgData['prd_seq'] = $paramPrd['prd_seq'] ?? 1;
                                $pkgData['created_by'] = $paramPrd['created_by'] ?? 'system';
                                $pkgData['pkg_seq'] = $pkgData['unitpkg_seq'];
                                $pkgData['qty'] = $pkgData['qty'] ?? 0;
                                $pkgData['baseqty'] = $paramPrd['baseqty'] ?? 0;

                                PurchaseOrderProductEntry::create($pkgData);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY STORE METHOD"]);
                    throw new \Exception($e->getMessage());
                }
            });
        });
    }

    public function destroy($id, $request)
    {
        return $this->executeDestroy(function () use ($id, $request) {

            return $this->baseRepository->destroy($id, $request);
        });
    }

    public function bulkEdit($data)
    {
        return $this->executeBulkEdit(function () use ($data) {
            return $this->baseRepository->bulkEdit($data);
        });
    }

    public function printIndex($request)
    {
        return $this->executePrintIndex(function () use ($request) {
            $request['params'] = $request['params'] ?? null;
            return $this->baseRepository->printIndex($request);
        });
    }

    public function importCsv($data)
    {
        return $this->executeImportCsv($data, function () use ($data) {
            return $this->baseRepository->importCsv($data);
        });
    }
}
