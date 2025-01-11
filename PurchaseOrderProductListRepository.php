<?php

namespace App\Repositories\Transaction;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Logs\MasterLogRepository;
use App\Models\Master\PurchaseEntryType;
use App\Models\Relations\Principal\Principal;
use App\Models\Relations\Product\MasterProduct;
use App\Models\Transaction\PurchaseOrderProductEntry;

class PurchaseOrderProductListRepository 
{
	protected $productLogRepository;

	function __construct()
	{
		$this->productLogRepository = new MasterLogRepository();
	}

	public function index($request)
	{
		  try {
			  // START QUERY
			  $query = MasterProduct::with(['proppiunipkg.unitpackaging', 'proppibuyprice.unitpackaging', 'proppibuyprice.buypricegroup']);

			  $text = "masuk";
			  $temp = $query->get();
			//   print_r($temp[0]['id']);
			//   exit();
			//   $query->with('proppiunipkg.proppibuyprice', function($q) {
			// 	return $q->whereColumn('unitpkg_id', 'proppiunipkg.unitpkg_id');
			// });
  
			  $searchKey = $request['search'];
			  $searchFields = $request['search_fields'];
			  $params = $request['params'];
			  $orderDirection = $request['order_direction'];
			  $orderField = $request['order_field'];
			  $limit = $request['limit'] ?? 100;
  
			  if($searchKey !== null){
				  $query->keywordSearch($searchKey, $searchFields);
			  }
  
			  if(!$params){
				  Log::info("NO PARAM IN INDEX METHOD");
				  $result = $query
					  ->orderBy($orderField, $orderDirection)
					  ->paginate($limit);
				  return $result;
			  }
  
  
			  $query->filterByParams($params);
			  $result = $query
				  ->orderBy($orderField, $orderDirection)
				  ->paginate($limit);
  
			  Log::info("RESULT WITH PARAM");
			  Log::info($result);
			  return $result; 
		  } catch (\Exception $e) {
			  Log::info("EROR");
			  Log::info($e);
			  throw new \Exception($e->getMessage());
		  }
	}
}