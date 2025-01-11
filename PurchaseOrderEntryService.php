<?php

namespace App\Services\Transaction\PurchaseOrder;

use App\Models\Master\PurchaseEntryType;
use App\Models\Relations\Principal\Principal;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderBillEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderDiscountDocEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderDiscountPrdEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderEntryStatus;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductEntryShip;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductSumEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Logs\MasterLogRepository;
use Eskalink\BeCoreBase\Services\Core\BaseService;
use Eskalink\BeCoreBase\Repositories\Core\BaseRepository;

class PurchaseOrderEntryService extends BaseService
{
    protected $baseRepository;
    protected $statusRepository;
    protected $MasterLogRepository;

    private $config = [
        "tableFields" => [
            'ent_type_id' => [
                "isRemap" => true,
                'relations' => 'pursteenttype',
                'model' => PurchaseEntryType::class,
                'select' => []
            ],
            'ent_type_desc' => [
                "isRemap" => false,
            ],
            'princ_id' => [
                "isRemap" => true,
                'relations' => 'pristeprofile',
                'model' => Principal::class,
                'select' => []
            ],
            'doc_no' => [
                "isRemap" => false,
            ],
            'doc_date' => [
                "isRemap" => false,
            ],
            'doc_ref' => [
                "isRemap" => false,
            ],
            'doc_remark' => [
                "isRemap" => false,
            ],
            'req_delv_date' => [
                "isRemap" => false,
            ],
            'prm_ship_date' => [
                "isRemap" => false,
            ],
            'prm_delv_date' => [
                "isRemap" => false,
            ],
            'is_active' => [
                "isRemap" => false,
            ],
        ]
    ];

    public function __construct()
    {
        $this->baseRepository = new BaseRepository(new PurchaseOrderEntry(), $this->config);
        $this->MasterLogRepository = new MasterLogRepository();
    }

    public function index($request)
    {
        return $this->executeIndex(function () use ($request) {
            $data = [];
            $headers = $this->baseRepository->index($request);
            
            foreach ($headers as $key => $value) {

                // CALL ENTRY SHIP
                $transferShip = new PurchaseOrderProductEntryShip();
                $headers[$key]['puransordentship'] = $transferShip->where("parent_doc_id", "=", $headers[$key]["id"])->first();

                // CALL BILL ENTRY
                $transferBill = new PurchaseOrderBillEntry();
                $headers[$key]['puransordentbill'] = $transferBill->where("parent_doc_id", "=", $headers[$key]["id"])->first();

                // CALL STATUS ENTRY
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
                        // CALL DOCUMENT PRODUCT INSIDE PRODUCT SUMMARY
                        $data = PurchaseOrderDiscountDocEntry::where("parent_doc_id", $headers[$key]['id'])
                                    ->where("prd_id", $innerValue["prd_id"])->get();
                        $headers[$key]["puransordentprdsum"][$innerKey]["puransordentdiscdoc"] = $data;
                        // CALL DISCOUNT PRODUCT INSIDE PRODUCT SUMMARY
                        $data = PurchaseOrderDiscountPrdEntry::where("parent_doc_id", $headers[$key]['id'])
                                    ->where("prd_id", $innerValue["prd_id"])->get();
                        $headers[$key]["puransordentprdsum"][$innerKey]["puransordentdiscprd"] = $data;
 
                    }
                }

                }
            return $headers;
        });
    }

    public function store($request)
    {
        return $this->executeStore(function () use ($request) {
            return DB::transaction(function () use ($request) {
                try {
                    $dataPurchaseStatus = isset($request['puransordentsts']) ? $request['puransordentsts'] : null;
                    $dataPurchaseShip = isset($request['puransordentship']) ? $request['puransordentship'] : null;
                    $dataBillEntry = isset($request['puransordentbill']) ? $request['puransordentbill'] : null;
                    $dataProductSum = isset($request['puransordentprdsum']) ? $request['puransordentprdsum'] : [];
                    $dataProductDiscDoc = isset($request['puransordentdiscdoc']) ? $request['puransordentdiscdoc'] : [];

                    if (isset($request)) {
                        $trfEntry = PurchaseOrderEntry::create($request);
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
                            $dataPurchaseShip['ship_to_flag'] = $dataPurchaseShip['ship_to_flag'] ?? 'W';
                            PurchaseOrderProductEntryShip::create($dataPurchaseShip);
                        }

                        foreach ($dataProductSum as $paramPrdSum) {
                            $paramPrdSum['parent_doc_id'] = $parentDocId;
                            $paramPrdSum['created_by'] = $paramPrdSum['created_by'] ?? 'system';
                            PurchaseOrderProductSumEntry::create($paramPrdSum);
                        }

                        foreach ($dataProductSum as $paramDisDoc) {
                            $paramDisDoc['parent_doc_id'] = $parentDocId;
                            $paramDisDoc['prd_seq'] = $paramDisDoc['prd_seq'] ?? 1;
                            $paramDisDoc['disc_rate_pct'] = $paramDisDoc['puransordentdiscdoc']['disc_rate_pct'] ?? 0;
                            $paramDisDoc['disc_rate_val'] = $paramDisDoc['puransordentdiscdoc']['disc_rate_val'] ?? 0;
                            $paramDisDoc['disc_rate_amt'] = $paramDisDoc['puransordentdiscdoc']['disc_rate_amt'] ?? 0;
                            $paramDisDoc['disc_in_val'] = $paramDisDoc['puransordentdiscdoc']['disc_in_val'] ?? 0;
                            $paramDisDoc['tot_disc_amt'] = $paramDisDoc['puransordentdiscdoc']['tot_disc_amt'] ?? 0;  
                            $paramDisDoc['created_by'] = $paramPrdSum['created_by'] ?? 'system';
                            PurchaseOrderDiscountDocEntry::create($paramDisDoc);
                        }

                        foreach ($dataProductSum as $paramPrd) {
                            foreach ($paramPrd['puransordentprd'] as &$pkgData) {
                                $pkgData['parent_doc_id'] = $parentDocId;
                                $pkgData['prd_id'] = $pkgData['prd_id'] ?? $paramPrd['prd_id'];
                                $pkgData['prd_code'] = $pkgData['prd_code'] ?? $paramPrd['prd_code'];
                                $pkgData['prd_shortdesc'] = $pkgData['prd_shortdesc'] ?? $paramPrd['prd_shortdesc'];
                                $pkgData['prd_fulldesc'] = $pkgData['prd_fulldesc'] ?? $paramPrd['prd_fulldesc'];
                                $pkgData['prd_seq'] = $paramPrd['prd_seq'] ?? 1;
                                $pkgData['created_by'] = $paramPrd['created_by'] ?? 'system';
                                $pkgData['pkg_seq'] = $pkgData['pkg_seq'] ?? $pkgData['unitpkg_seq'];
                                $pkgData['qty'] = $pkgData['qty'] ?? 0;
                                $pkgData['baseqty'] = $paramPrd['baseqty'] ?? 0;
                                $pkgData['convpkg'] = $paramPrd['convpkg'] ?? 0;

                                PurchaseOrderProductEntry::create($pkgData);
                                
                            }
                            // foreach ($paramPrd['puransordentdiscdoc'] as &$disDocData) {
                            //             // print_r($paramPrd['proppiunipkg']);
                            //             // exit(); 
                            //             $disDocData['parent_doc_id'] = $parentDocId;
                            //             $disDocData['prd_id'] = $disDocData['prd_id'] ?? $paramPrd['prd_id'];
                            //             $disDocData['prd_seq'] = $paramPrd['prd_seq'] ?? 1;
                            //             $disDocData['disc_rate_pct'] = $disDocData['disc_rate_pct'] ?? 0;
                            //             $disDocData['disc_rate_val'] = $disDocData['disc_rate_val'] ?? 0;
                            //             $disDocData['disc_rate_amt'] = $disDocData['disc_rate_amt'] ?? 0;
                            //             $disDocData['disc_in_val'] = $disDocData['disc_in_val'] ?? 0;
                            //             $disDocData['tot_disc_amt'] = $disDocData['tot_disc_amt'] ?? 0;                             
        
                            //             PurchaseOrderDiscountDocEntry::create($disDocData);
                            //         }
                        }

                        // foreach ($dataProductSum as $paramDisdoc) {
                        //     foreach ($paramDisdoc['puransordentdiscdoc'] as &$disDocData) {
                        //         // // print_r($paramPrd['proppiunipkg']);
                        //         // // exit(); 
                        //         $disDocData['parent_doc_id'] = $parentDocId;
                        //         $disDocData['prd_id'] = $disDocData['prd_id'] ?? $paramDisdoc['prd_id'];
                        //         $disDocData['prd_seq'] = $paramDisdoc['prd_seq'] ?? 1;
                        //         $disDocData['disc_rate_pct'] = $disDocData['disc_rate_pct'] ?? 0;
                        //         $disDocData['disc_rate_val'] = $disDocData['disc_rate_val'] ?? 0;
                        //         $disDocData['disc_rate_amt'] = $disDocData['disc_rate_amt'] ?? 0;
                        //         $disDocData['disc_in_val'] = $disDocData['disc_in_val'] ?? 0;
                        //         $disDocData['tot_disc_amt'] = $disDocData['tot_disc_amt'] ?? 0;   

                        //         PurchaseOrderDiscountDocEntry::create($disDocData);
                        //     }
                        // }

                        // foreach ($dataProductSum as $paramDisPrd) {
                        //     foreach ($paramDisPrd['puransordentdiscprd'] as &$disPrdData) {
                        //         $disPrdData['parent_doc_id'] = $parentDocId;
                        //         $disPrdData['prd_id'] = $disPrdData['prd_id'] ?? $paramDisPrd['prd_id'];
                        //         $disPrdData['prd_seq'] = $paramDisPrd['prd_seq'] ?? 1;
                        //         $disPrdData['level_id'] = $disPrdData['level_id'];
                        //         $disPrdData['level_seq'] = $disPrdData['level_seq'];
                        //         $disPrdData['level_name'] = $disPrdData['level_name'];
                        //         $disPrdData['base_disc'] = $disPrdData['base_disc'];
                        //         $disPrdData['bef_disc_amt'] = $disPrdData['bef_disc_amt'];
                        //         $disPrdData['disc_id'] = $disPrdData['disc_id'];
                        //         $disPrdData['disc_rate_pct'] = $disPrdData['disc_rate_pct'] ?? 0;
                        //         $disPrdData['disc_rate_val'] = $disPrdData['disc_rate_val'] ?? 0;
                        //         $disPrdData['disc_rate_amt'] = $disPrdData['disc_rate_amt'] ?? 0;
                        //         $disPrdData['disc_in_val'] = $disPrdData['disc_in_val'] ?? 0;
                        //         $disPrdData['tot_disc_amt'] = $disPrdData['tot_disc_amt'] ?? 0; 
                        //         $disPrdData['aft_disc_amt'] = $disPrdData['aft_disc_amt'] ?? 0;                                

                        //         PurchaseOrderDiscountPrdEntry::create($disPrdData);
                        //     }
                        // }
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

                $transferEntry["puransordentship"] = PurchaseOrderProductEntryShip::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentbill"] = PurchaseOrderBillEntry::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentship"] = PurchaseOrderProductEntryShip::where("parent_doc_id", $id)->first();

                $transferEntry["puransordentprdsum"] = PurchaseOrderProductSumEntry::where("parent_doc_id", $id)->get();
                
                foreach($transferEntry["puransordentprdsum"] as $key => $value) {
                    $transferEntry["puransordentdoc"]["doc_date"] = '2024-03-18';
                    $data = PurchaseOrderProductEntry::where("parent_doc_id", $id)
                                ->where("prd_id", $transferEntry["puransordentprdsum"][$key]["prd_id"])->get();
                    $transferEntry["puransordentprdsum"][$key]["puransordentprd"] = $data;
                    
                    $data = PurchaseOrderDiscountDocEntry::where("parent_doc_id", $id)
                                ->where("prd_id", $transferEntry["puransordentprdsum"][$key]["prd_id"])->get();
                    $transferEntry["puransordentprdsum"][$key]["puransordentdiscdoc"] = $data;

                    $data = PurchaseOrderDiscountPrdEntry::where("parent_doc_id", $id)
                                ->where("prd_id", $transferEntry["puransordentprdsum"][$key]["prd_id"])->get();
                    $transferEntry["puransordentprdsum"][$key]["puransordentdiscprd"] = $data;
                }
            }
            return $transferEntry;
        });
    }

    public function update($request, $id)
    {
        return $this->executeUpdate(function () use ($request, $id) {
            return $this->baseRepository->update($request, $id);
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
