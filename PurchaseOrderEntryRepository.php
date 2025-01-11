<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction\PurchaseOrder\PurchaseOrderEntry;
use App\Models\Transaction\PurchaseOrder\PurchaseOrderProductEntry;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Logs\MasterLogRepository;
use App\Models\Relations\Principal\Principal;

class PurchaseOrderEntryRepository 
{
	protected $productLogRepository;

	function __construct()
	{
		$this->productLogRepository = new MasterLogRepository();
	}

	public function index(array $request)
	{
		$query = PurchaseOrderEntry::query();
		$params = isset($request['params']) ? json_decode($request['params'], true) : [];
		$query->filterByParams($params);

		if (isset($request['search'])) {
			$searchKeyword = $request['search'];
			$searchFields = json_decode($request['search_fields'], true) ?? [];
			$query->keywordSearch($searchKeyword, $searchFields);
		}

		if (isset($request['sort'])) {
			$orderedColumn = json_decode($request['sort'], true);
			$column = $orderedColumn[0] ?? 'created_at';
			$order = $orderedColumn[1] ?? 'asc';
			$query->orderBy($column, $order);
		} else {
			$query->orderBy('created_at', 'desc');
		}

		if (isset($request['limit']) || isset($request['page'])) {
			$limit = $request['limit'] ?? 10;
			$result = $query->with([
			 'puransordentsts.purstereqsts',
			 'puransordentprd.prostebatch',
			 'puransordentprd.unitpackaging',
			 'puransordentprd.basepackaging',
			 'puransordentprd.prostebuyprigroup',
			 'puransordentprd.proppibuyprice',

			 'puransordentprdsum.smallpackaging',
			 'puransordentprdsum.basepackaging',
			 
			 'puransordentship.ivtstewarehouse',
			 'puransordentship.borstelocd',
			 'puransordentship.whcountry',
			 'puransordentship.whprovince',
			 'puransordentship.whcity',
			 'puransordentship.whdistrict',
			 'puransordentship.whsubdistrict',
			 'puransordentship.whzipsubdistrict',
			 'puransordentship.cussteprofile',
			 'puransordentship.cusppiprodeladdress',
			 'puransordentship.cscountry',
			 'puransordentship.csprovince',
			 'puransordentship.cscity',
			 'puransordentship.csditrict',
			 'puransordentship.cssubdistrict',
			 'puransordentship.cszipsubdistrict',

			 'puransordentbill.tpystetop',
			 'puransordentbill.tpystetype',
			 'puransordentbill.taxsteh',
			 'puransordentbill.taxsted',
			 ])->paginate($limit);
		} else {
			$result = $query->with([
			'puransordentsts.purstereqsts',
			'puransordentprd.prostebatch',
			'puransordentprd.unitpackaging',
			'puransordentprd.basepackaging',
			'puransordentprd.prostebuyprigroup',
			'puransordentprd.proppibuyprice',
			
			'puransordentship.ivtstewarehouse',
			'puransordentship.borstelocd',
			'puransordentship.whcountry',
			'puransordentship.whprovince',
			'puransordentship.whcity',
			'puransordentship.whdistrict',
			'puransordentship.whsubdistrict',
			'puransordentship.whzipsubdistrict',
			'puransordentship.cussteprofile',
			'puransordentship.cusppiprodeladdress',
			'puransordentship.cscountry',
			'puransordentship.csprovince',
			'puransordentship.cscity',
			'puransordentship.csditrict',
			'puransordentship.cssubdistrict',
			'puransordentship.cszipsubdistrict',

			'puransordentbill.tpystetop',
			'puransordentbill.tpystetype',
			'puransordentbill.taxsteh',
			'puransordentbill.taxsted',
			
			])->get(); // Untuk Print dan Download
		}

		return $result;
	}

	public function store($request)
	{
		return DB::transaction(function() use ($request){
			try {
				$purchaseEntry = PurchaseOrderEntry::create([
					'ent_type_id' => $request ['ent_type_id'],
					'ent_type_desc' => $request ['ent_type_desc'],
					'princ_id' => $request ['princ_id'],
					'princ_code' => $request ['princ_code'],
					'princ_shortname' => $request ['princ_shortname'],
					'princ_fullname' => $request ['princ_fullname'],
					'doc_no' => $request ['doc_no'],
					'doc_date' => $request ['doc_date'],
					'doc_ref' => $request ['doc_ref'],
					'doc_remark' => $request ['doc_remark'],
					'rqs_doc_id' => $request ['rqs_doc_id'],
					'rqs_doc_no' => $request ['rqs_doc_no'],
					'req_delv_date' => $request ['req_delv_date'],
					'prm_ship_date' => $request ['prm_ship_date'],
					'prm_delv_date' => $request ['prm_delv_date'],
					'is_active' => $request ['is_active'],
					'created_at' => $request ['created_at'],
					'created_by' => $request ['created_by'],
				]);
				if(isset($request['puransordentsts']))  {
					$purchaseEntry->puransordentsts()->create([
						'parent_doc_id' => $purchaseEntry->id,
						'doc_sts' => $request['puransordentsts']['doc_sts'] ?? null,
						'sts_shortdesc' => $request['puransordentsts']['sts_shortdesc'] ?? null,
						'doc_remark' => $request['puransordentsts']['doc_remark'] ?? null,
						'is_active' => $request['puransordentsts']['is_active'] ?? null,
						'created_by' => $request['puransordentsts']['created_by'] ?? 'Danies',
					]);
				}
				if(isset($request['puransordentship']))  {
					$purchaseEntry->puransordentship()->create([
						'parent_doc_id' => $purchaseEntry->id,
						'ship_to_flag' => $request['puransordentship']['ship_to_flag'] ?? null,
						'wh_dest_id' => $request['puransordentship']['wh_dest_id'] ?? null,
						'wh_code' => $request['puransordentship']['wh_code'] ?? null,
						'wh_shortname' => $request['puransordentship']['wh_shortname'] ?? null,
						'wh_fullname' => $request['puransordentship']['wh_fullname'] ?? null,
						'wh_loc_id' => $request['puransordentship']['wh_loc_id'] ?? null,
						'wh_address1' => $request['puransordentship']['wh_address1'] ?? null,
						'wh_address2' => $request['puransordentship']['wh_address2'] ?? null,
						'wh_address3' => $request['puransordentship']['wh_address3'] ?? null,
						'wh_country_id' => $request['puransordentship']['wh_country_id'] ?? null,
						'wh_prov_id' => $request['puransordentship']['wh_prov_id'] ?? null,
						'wh_city_id' => $request['puransordentship']['wh_city_id'] ?? null,
						'wh_dist_id' => $request['puransordentship']['wh_dist_id'] ?? null,
						'wh_sdist_id' => $request['puransordentship']['wh_sdist_id'] ?? null,
						'wh_zip_code' => $request['puransordentship']['wh_zip_code'] ?? null,
						'cust_id' => $request['puransordentship']['cust_id'] ?? null,
						'cs_code' => $request['puransordentship']['cs_code'] ?? null,
						'cs_shortname' => $request['puransordentship']['cs_shortname'] ?? null,
						'cs_fullname' => $request['puransordentship']['cs_fullname'] ?? null,
						'cust_addr_id' => $request['puransordentship']['cust_addr_id'] ?? null,
						'cs_addr_name' => $request['puransordentship']['cs_addr_name'] ?? null,
						'cs_address1' => $request['puransordentship']['cs_address1'] ?? null,
						'cs_address2' => $request['puransordentship']['cs_address2'] ?? null,
						'cs_address3' => $request['puransordentship']['cs_address3'] ?? null,
						'cs_country_id' => $request['puransordentship']['cs_country_id'] ?? null,
						'cs_prov_id' => $request['puransordentship']['cs_prov_id'] ?? null,
						'cs_city_id' => $request['puransordentship']['cs_city_id'] ?? null,
						'cs_dist_id' => $request['puransordentship']['cs_dist_id'] ?? null,
						'cs_sdist_id' => $request['puransordentship']['cs_sdist_id'] ?? null,
						'cs_zip_code' => $request['puransordentship']['cs_zip_code'] ?? null,
						'created_by' => $request['puransordentship']['created_by'] ?? 'Danies',
					]);
				}
				if(isset($request['puransordentbill']))  {
					$purchaseEntry->puransordentbill()->create([
						'parent_doc_id' => $purchaseEntry->id,
						'top_id' => $request['puransordentbill']['top_id'] ?? null,
						'top_code' => $request['puransordentbill']['top_code'] ?? null,
						'top_shortdesc' => $request['puransordentbill']['top_shortdesc'] ?? null,
						'top_fulldesc' => $request['puransordentbill']['top_fulldesc'] ?? null,
						'top_top_days' => $request['puransordentbill']['top_top_days'] ?? null,
						'paytp_id' => $request['puransordentbill']['paytp_id'] ?? null,
						'paytp_code' => $request['puransordentbill']['paytp_code'] ?? null,
						'paytp_shortdesc' => $request['puransordentbill']['paytp_shortdesc'] ?? null,
						'paytp_fulldesc' => $request['puransordentbill']['paytp_fulldesc'] ?? null,
						'curr_id' => $request['puransordentbill']['curr_id'] ?? null,
						'curr_code' => $request['puransordentbill']['curr_code'] ?? null,
						'curr_shortdesc' => $request['puransordentbill']['curr_shortdesc'] ?? null,
						'curr_fulldesc' => $request['puransordentbill']['curr_fulldesc'] ?? null,
						'tax_id' => $request['puransordentbill']['tax_id'] ?? null,
						'tax_code' => $request['puransordentbill']['tax_code'] ?? null,
						'tax_shortdesc' => $request['puransordentbill']['tax_shortdesc'] ?? null,
						'tax_fulldesc' => $request['puransordentbill']['tax_fulldesc'] ?? null,
						'tax_rate_id' => $request['puransordentbill']['tax_rate_id'] ?? null,
						'tax_rate_val' => $request['puransordentbill']['tax_rate_val'] ?? null,
						'created_by' => $request['puransordentbill']['created_by'] ?? 'root',
					]);
				}
				$purPrd = isset($request['puransordentprd']) ? $request['puransordentprd'] : [];
				foreach($purPrd as $paramPrd) {
				    $puransordentprd = new PurchaseOrderProductEntry();
				    $puransordentprd->parent_doc_id = $purchaseEntry->id;
				    $puransordentprd->prd_id = $paramPrd['id'] ?? null;
				    $puransordentprd->prd_code = $paramPrd['code'] ?? 'FG11302-146-004L-G';
				    $puransordentprd->prd_shortdesc = $paramPrd['shortdesc'] ?? 'SLEEK BLD GLN 4L';
				    $puransordentprd->prd_fulldesc = $paramPrd['fulldesc'] ?? 'SLEEK BLD GLN 4L';
				    $puransordentprd->prd_seq = $paramPrd['seq'] ?? 1;
				    $puransordentprd->batch_id = $paramPrd['batch_id'] ?? null;
				    $puransordentprd->batch_code = $paramPrd['code'] ?? null;
				    $puransordentprd->batch_exp_date = $paramPrd['exp_date'] ?? null;
				    $puransordentprd->batch_mfc_date = $paramPrd['mfc_date'] ?? null;
				    $puransordentprd->batch_srno = $paramPrd['srno'] ?? null;
				    $puransordentprd->batch_seq = $paramPrd['seq'] ?? null;
				    $puransordentprd->pkg_id = $paramPrd['pkg_id'] ?? '7DC8BB8A-5AB6-40D4-ACE3-6CB9483753AB';
				    $puransordentprd->pkg_code = $paramPrd['pkg_code'] ?? 'GUDANG';
				    $puransordentprd->pkg_shortdesc = $paramPrd['pkg_shortdesc'] ?? 'STAR ASIA';
				    $puransordentprd->pkg_fulldesc = $paramPrd['pkg_fulldesc'] ?? 'PT CABANG PUSAT';
				    $puransordentprd->pkg_seq = $paramPrd['pkg_seq'] ?? 1;
				    $puransordentprd->qty = $paramPrd['qty'] ?? 5;
				    $puransordentprd->basepkg_id = $paramPrd['basepkg_id'] ?? '2B8677E1-F6A4-4F59-8C5C-70222315C561';
				    $puransordentprd->basepkg_code = $paramPrd['basepkg_code'] ?? 'GIWQJA08EQXF0NX';
				    $puransordentprd->basepkg_shortdesc = $paramPrd['basepkg_shortdesc'] ?? 'PICIS';
				    $puransordentprd->basepkg_fulldesc = $paramPrd['basepkg_fulldesc'] ?? 'PICIS';
				    $puransordentprd->convpkg = $paramPrd['convpkg'] ?? 8;
				    $puransordentprd->baseqty = $paramPrd['baseqty'] ?? 6;
				    $puransordentprd->prc_id = $paramPrd['prc_id'] ?? 'B202350D-840E-45EA-BD76-3C3B3772985D';
				    $puransordentprd->prc_code = $paramPrd['prc_code'] ?? 'PG.WT/09-LE_TEST';
				    $puransordentprd->prc_shortdesc = $paramPrd['prc_shortdesc'] ?? 'GNNB-79DA';
				    $puransordentprd->prc_fulldesc = $paramPrd['prc_fulldesc'] ?? 'PARIATUR POSSIMUS ET ASPERIORES RERUM ILLO';
				    $puransordentprd->prc_price_id = $paramPrd['prc_price_id'] ?? '899A2E59-F44E-4BC6-B05F-B71A3E712143';
				    $puransordentprd->prc_price_amt = $paramPrd['prc_price_amt'] ?? 18000;
				    $puransordentprd->gross_amt = $paramPrd['gross_amt'] ?? 8;
				    $puransordentprd->created_by = $paramPrd['created_by'] ?? 'ADMIN';
				
				    $puransordentprd->save();
				}


				return $purchaseEntry;
			} catch (\Exception $e) {
                Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY STORE METHOD"]);
                throw new \Exception($e->getMessage());
            }
		});
	}

	public function show($id)
	{
		try {
            $result = PurchaseOrderEntry::where('id', $id)->first();
            return $result;
        } catch (\Exception $e) {
			Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY SHOW METHOD"]);
            throw new \Exception($e->getMessage());
        }
	}

	public function update($request, $id)
	{
		return DB::transaction(function () use ($request, $id) {
            try {
                $hierarchy = PurchaseOrderEntry::where('id', $id)->first();

                $hierarchy->update($request);

                return $hierarchy;
            } catch (\Exception $e) {
				Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY UPDATE METHOD"]);
                throw new \Exception($e->getMessage());
            }
        });
	}

	public function destroy($id, $deletedBy)
	{
		return DB::transaction(function () use ($id, $deletedBy) {
            try {
                $hierarchy = $this->show($id);
                $hierarchy->delete();
                return $hierarchy;
            } catch (\Exception $e) {
				Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY DESTROY METHOD"]);
                throw new \Exception($e->getMessage());
            }
        });
	}

	public function printIndex($request)
	{
		try {
			$request['params'] = isset($request['params']) ? json_decode($request['params'], true) : null;

            $collection = new Collection();
            $query = PurchaseOrderEntry::query();

            if(!$request['params']) {
                $hierarchy = $query
                    ->chunk(100, function ($rows) use ($collection){
                        foreach($rows as $row){
                            $collection->push($row);
                        }
                    });
                return $collection;
            }
            
            $filteredData = $query
                ->filterByParams($request['params'])
                ->chunk(100, function ($rows) use ($collection){
                    foreach($rows as $row){
                        $collection->push($row);
                    }
                });

            return $collection;
        } catch (\Exception $e) {
            Log::error($e, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY PRINT INDEX METHOD"]);
            throw new \Exception($e->getMessage());
        }
	}

	function importCsv($data)
	{
		$chunkSize = 250; // 250 data per chunk

		return DB::transaction(function () use ($data, $chunkSize) {
			try {
				$chunks = array_chunk($data, $chunkSize);

				foreach ($chunks as $chunk) {
					PurchaseOrderEntry::insert($chunk);
				}

				return true;
			} catch (\Throwable $th) {
				Log::error($th, ["ERROR: LAYER REPOSITORY PURCHASE ENTRY IMPORT CSV METHOD"]);
				throw $th;
			}
		});
	}
}