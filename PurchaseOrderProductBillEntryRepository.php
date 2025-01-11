<?php

// namespace App\Repositories\Transaction;

// use Illuminate\Support\Str;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use App\Repositories\Logs\MasterLogRepository;
// use App\Models\Master\PurchaseEntryType;
// use App\Models\Relations\Principal\Principal;
// use App\Models\Transaction\PurchaseOrderBillEntry;

// class PurchaseOrderProductBillEntryRepository 
// {
// 	protected $productLogRepository;

// 	function __construct()
// 	{
// 		$this->productLogRepository = new MasterLogRepository();
// 	}

// 		public function index($request)
// 	{
// 		  try {
// 			  // START QUERY
// 			  $query = MasterProduct::with(['proppiunipkg.unitpackaging', 'proppibuyprice.unitpackaging', 'proppibuyprice.buypricegroup' ]);
  
// 			  $searchKey = $request['search'];
// 			  $searchFields = $request['search_fields'];
// 			  $params = $request['params'];
// 			  $orderDirection = $request['order_direction'];
// 			  $orderField = $request['order_field'];
// 			  $limit = $request['limit'] ?? 100;
  
// 			  if($searchKey !== null){
// 				  $query->keywordSearch($searchKey, $searchFields);
// 			  }
  
// 			  if(!$params){
// 				  Log::info("NO PARAM IN INDEX METHOD");
// 				  $result = $query
// 					  ->orderBy($orderField, $orderDirection)
// 					  ->paginate($limit);
// 				  return $result;
// 			  }
  
  
// 			  $query->filterByParams($params);
// 			  $result = $query
// 				  ->orderBy($orderField, $orderDirection)
// 				  ->paginate($limit);
  
// 			  Log::info("RESULT WITH PARAM");
// 			  Log::info($result);
// 			  return $result; 
// 		  } catch (\Exception $e) {
// 			  Log::info("EROR");
// 			  Log::info($e);
// 			  throw new \Exception($e->getMessage());
// 		  }
// 	}

// 	public function store($request)
// 	{
// 		return DB::transaction(function() use ($request){
// 			try {
// 				$result = PurchaseOrderBillEntry::create($request);

// 				return $result;
// 			} catch (\Exception $e) {
//                 Log::error($e, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY STORE METHOD"]);
//                 throw new \Exception($e->getMessage());
//             }
// 		});
// 	}

// 	public function show($id)
// 	{
// 		try {
//             $result = PurchaseOrderBillEntry::where('id', $id)->first();
//             return $result;
//         } catch (\Exception $e) {
// 			Log::error($e, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY SHOW METHOD"]);
//             throw new \Exception($e->getMessage());
//         }
// 	}

// 	public function update($request, $id)
// 	{
// 		return DB::transaction(function () use ($request, $id) {
//             try {
//                 $hierarchy = PurchaseOrderBillEntry::where('id', $id)->first();

//                 $hierarchy->update($request);

//                 return $hierarchy;
//             } catch (\Exception $e) {
// 				Log::error($e, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY UPDATE METHOD"]);
//                 throw new \Exception($e->getMessage());
//             }
//         });
// 	}

// 	public function destroy($id, $deletedBy)
// 	{
// 		return DB::transaction(function () use ($id, $deletedBy) {
//             try {
//                 $hierarchy = $this->show($id);
//                 $hierarchy->delete();
//                 return $hierarchy;
//             } catch (\Exception $e) {
// 				Log::error($e, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY DESTROY METHOD"]);
//                 throw new \Exception($e->getMessage());
//             }
//         });
// 	}

// 	public function printIndex($request)
// 	{
// 		try {
// 			$request['params'] = isset($request['params']) ? json_decode($request['params'], true) : null;

//             $collection = new Collection();
//             $query = PurchaseOrderBillEntry::query();

//             if(!$request['params']) {
//                 $hierarchy = $query
//                     ->chunk(100, function ($rows) use ($collection){
//                         foreach($rows as $row){
//                             $collection->push($row);
//                         }
//                     });
//                 return $collection;
//             }
            
//             $filteredData = $query
//                 ->filterByParams($request['params'])
//                 ->chunk(100, function ($rows) use ($collection){
//                     foreach($rows as $row){
//                         $collection->push($row);
//                     }
//                 });

//             return $collection;
//         } catch (\Exception $e) {
//             Log::error($e, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY PRINT INDEX METHOD"]);
//             throw new \Exception($e->getMessage());
//         }
// 	}

// 	function importCsv($data)
// 	{
// 		$chunkSize = 250; // 250 data per chunk

// 		return DB::transaction(function () use ($data, $chunkSize) {
// 			try {
// 				$chunks = array_chunk($data, $chunkSize);

// 				foreach ($chunks as $chunk) {
// 					PurchaseOrderBillEntry::insert($chunk);
// 				}

// 				return true;
// 			} catch (\Throwable $th) {
// 				Log::error($th, ["ERROR: LAYER REPOSITORY PRODUCT PURCHASE ENTRY IMPORT CSV METHOD"]);
// 				throw $th;
// 			}
// 		});
// 	}
// }