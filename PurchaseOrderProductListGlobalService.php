<?php

namespace App\Services\Transaction\PurchaseOrder;

use App\Models\Mapping\MappingDiscPrincipalProduct;
use App\Models\Mapping\PurchaseOrderEntryTypePrincipal\PurchaseOrderEntryTypePrincipal;
use App\Models\Relations\Inventory\StockCategory;
use App\Models\Relations\Inventory\StockCategoryStatus;
use App\Models\Relations\Inventory\StockCategoryWarehouseType;
use App\Models\Relations\Inventory\StockStatus;
use App\Models\Relations\Principal\Principal;
use App\Models\Relations\Product\Mapping\ProductBuyPrice\ProductBuyPrice;
use App\Models\Relations\Product\Mapping\ProductPackaging\MappingBasePackagingUnit;
use App\Models\Relations\Product\Mapping\ProductPackaging\MappingPackagingUnit;
use App\Models\Relations\Product\Mapping\ProductPackaging\PackagingUnit;
use App\Models\Relations\Product\Mapping\ProductPrincipal\ProductPrincipal;
use App\Models\Master\DiscPrincipalProduct\DiscPrincipalProduct;
use App\Models\Relations\Product\Mapping\ProductWarehouse\ProductWarehouse;
use App\Models\Relations\Product\Master\Product\Product;
use App\Models\Relations\Warehouse\WarehouseMaster;
use App\Models\Relations\Warehouse\WarehouseType;
use Carbon\Carbon;
use Eskalink\BeCoreBase\Repositories\Core\BaseRepository;
use Eskalink\BeCoreBase\Repositories\Logs\MasterLogRepository;
use Eskalink\BeCoreBase\Services\Core\BaseService;
use Illuminate\Support\Facades\DB;

class PurchaseOrderProductListGlobalService extends BaseService
{
    protected $MasterLogRepository;
    protected $baseRepository;
    private $config = [

        "tableFields" => []
    ];

    function __construct()
    {
        $this->baseRepository = new BaseRepository(new Product(), $this->config);
        $this->MasterLogRepository = new MasterLogRepository();
    }

    public function index($request)
    {
        return $this->executeIndex(function () use ($request) {
            $products = $this->baseRepository->index($request, function($query) use ($request) {
                $prdprinc = ProductPrincipal::where('eff_date', '<=', DB::raw("'".Carbon::now()->format("Y-m-d")."'"))
                ->where('eff_date', '<=', DB::raw("'".Carbon::now()->format("Y-m-d")."'"));
                if (!empty($request['prd_id'])) {
                    $prdprinc->where('prd_id', $request['prd_id']);
                }
                $prdprinc->where('princ_id', $request['princ_id']);
                $prdprinc->select(
                'proppiprincipal.princ_id',
                'proppiprincipal.prd_id',
            );

                $prdwh = ProductWarehouse::join('cte_prodprofile', 'cte_prodprofile.prd_id', '=', 'proppiwrhouse.prd_id')
                ->join('ivtstewarehouse', 'proppiwrhouse.wh_id', '=', 'ivtstewarehouse.id')
                ->where('wh_id', $request['wh_id'])
                ->select(
                    'proppiwrhouse.prd_id',
                    'proppiwrhouse.wh_id',
                    'ivtstewarehouse.code as wh_code',
                    'ivtstewarehouse.shortname as wh_shortname',
                    'ivtstewarehouse.fullname as wh_fullname',
                    'ivtstewarehouse.whtype_id'

                );
        
                // $wh = WarehouseMaster::join('cte_prodwh', 'cte_prodwh.wh_id', '=', 'ivtstewarehouse.id')
                //     ->select(
                //         'ivtstewarehouse.id',
                //         'ivtstewarehouse.code as wh_code',
                //         'ivtstewarehouse.shortname as wh_shortname',
                //         'ivtstewarehouse.fullname as wh_fullname',
                //         'ivtstewarehouse.whtype_id'
                //     );
        
                $whtype = WarehouseType::join('cte_prodwh', 'cte_prodwh.whtype_id', '=', 'ivtstewhtype.id')
                    ->select(
                        'ivtstewhtype.id',
                        'ivtstewhtype.code as whtype_code',
                        'ivtstewhtype.description as whtype_description',
                    );
        
                $stockwhtype = StockCategoryWarehouseType::join('cte_whtype', 'cte_whtype.id', '=', 'ivtstestocatwhtype.whtype_id')
                    ->select(
                        'ivtstestocatwhtype.cat_id',
                        'ivtstestocatwhtype.whtype_id'
                    );
        
                $stockcat = StockCategory::join('cte_whtypecat', 'cte_whtypecat.cat_id', '=', 'ivtstestocat.id')
                    ->select(
                        'ivtstestocat.id',
                        'ivtstestocat.code as stocat_code',
                        'ivtstestocat.description as stocat_description',
                    );
                $stockcatsts = StockCategoryStatus::join('cte_stockcat', 'cte_stockcat.id', '=', 'ivtstestocatsts.cat_id')
                    ->join('ivtstestosts', 'ivtstestosts.code', '=', 'ivtstestocatsts.sts_code')
                    ->select(
                        'ivtstestocatsts.cat_id',
                        'ivtstestocatsts.sts_code',
                        'ivtstestosts.shortdesc as stocatsts_shortdesc',
                        'ivtstestosts.seq as stocatsts_seq',
                    );    
        
                return $query->withExpression('cte_prodprofile', $prdprinc)
                    ->withExpression('cte_prodwh', $prdwh)
                    // ->withExpression('cte_wh', $wh)
                    ->withExpression('cte_whtype', $whtype)
                    ->withExpression('cte_whtypecat', $stockwhtype)
                    ->withExpression('cte_stockcat', $stockcat)
                    ->withExpression('cte_stockcatsts', $stockcatsts)
                    ->join('cte_prodwh', 'cte_prodwh.prd_id', '=', 'proste.id')
                    ->join('cte_prodprofile', 'cte_prodprofile.prd_id', '=', 'proste.id')
                    // ->join('cte_wh', 'cte_wh.id', '=', 'cte_prodwh.wh_id')
                    ->join('cte_whtype', 'cte_whtype.id', '=', 'cte_prodwh.whtype_id')
                    ->join('cte_whtypecat', 'cte_whtypecat.whtype_id', '=', 'cte_whtype.id')
                    ->join('cte_stockcat', 'cte_stockcat.id', '=', 'cte_whtypecat.cat_id')
                    ->join('cte_stockcatsts', 'cte_stockcatsts.cat_id', '=', 'cte_whtypecat.cat_id')
                    ->select(
                        'proste.id as prd_id',
                        'proste.*',
                        'cte_prodprofile.*',
                        'cte_prodwh.*',
                        // 'cte_whtype.*',
                        // 'cte_whtypecat.*',
                        // 'cte_stockcat.*',
                        // 'cte_stockcatsts.*'
                    )
                    ->distinct(["proste.id"])
                    ->where("proste.is_active", "=", 1);
            });
            foreach ($products as $key => $product) {
                $proppiunipkg = MappingBasePackagingUnit::where('prd_id', $product['id'])
                    ->join('prosteunipackaging', 'prosteunipackaging.id', '=', 'proppiunibaspkg.basepkg_id')
                    ->join('proppiunipkg', 'proppiunibaspkg.id', '=', 'proppiunipkg.unitbaspkg_id')
                    ->where("proppiunibaspkg.eff_date", "<=", Carbon::now()->format("Y-m-d"))
                    ->where("proppiunibaspkg.eff_date", "<=", Carbon::now()->format("Y-m-d"))
                    ->where("proppiunibaspkg.deleted_at", "=", null)
                    ->where("prosteunipackaging.is_active", "=", 1)
                    ->orderBy('proppiunipkg.unitpkg_seq', 'desc')
                    ->select(
                        'proppiunibaspkg.*',
                        DB::raw('(SELECT id FROM prosteunipackaging WHERE id = proppiunipkg.unitpkg_id) as pkg_id'),
                        DB::raw('(SELECT code FROM prosteunipackaging WHERE id = proppiunipkg.unitpkg_id) as pkg_code'),
                        DB::raw('(SELECT shortdesc FROM prosteunipackaging WHERE id = proppiunipkg.unitpkg_id) as pkg_shortdesc'),
                        DB::raw('(SELECT fulldesc FROM prosteunipackaging WHERE id = proppiunipkg.unitpkg_id) as pkg_fulldesc'),
                        'proppiunipkg.convpkg',
                        'proppiunipkg.unitpkg_seq as pkg_seq',
                        'prosteunipackaging.id as basepkg_id',
                        'prosteunipackaging.code as basepkg_code',
                        'prosteunipackaging.shortdesc as basepkg_shortdesc',
                        'prosteunipackaging.fulldesc as basepkg_fulldesc'
                    )
                    ->get();
                $products[$key]['packaging'] = $proppiunipkg;
                foreach ($products[$key]['packaging'] as $skey => $value) {
                    $proppibuyprice = ProductBuyPrice::where([
                        ['prd_id', $product['id']],
                        ['unitpkg_id', $products[$key]['packaging'][$skey]['pkg_id']]
                    ])
                    ->where("eff_date", "<=", Carbon::now()->format("Y-m-d"))
                    ->where("deleted_at", "=", null)->get();

                    $products[$key]['packaging'][$skey]['buyprice'] = $proppibuyprice;
                }
            }

            foreach ($products as $key => $product) {
                $purstedispriprd = MappingDiscPrincipalProduct::where('prd_id', $product['id'])
                    ->join('purstedislevel', 'purstedislevel.id', '=', 'purstedispriprd.disc_id')
                    ->where("purstedispriprd.eff_date", "<=", Carbon::now()->format("Y-m-d"))
                    ->where("purstedispriprd.deleted_at", "=", null)
                    ->where("purstedislevel.is_active", "=", 1)
                    ->orderBy('purstedislevel.level_seq', 'desc')
                    ->select(
                        'def_disc_pct as disc_rate_pct',
                        'def_disc_val as disc_in_val',
                        'purstedispriprd.*',
                        'purstedislevel.id as level_id',
                        'purstedislevel.level_seq as level_seq',
                        'purstedislevel.level_name as level_name',
                        'purstedislevel.base_disc as base_disc',
                    )
                    ->get();
                $products[$key]['disountprd'] = $purstedispriprd;
            }
                    // Map prd_id to id
            // $products = $products->map(function ($product) {
            //     $product['id'] = $product['prd_id'];
            //     return $product;
            // });
            return $products;
        });
    }
    
    
}