<?php

namespace App\Services\Transaction\PurchaseOrder;

use App\Models\Mapping\MappingDiscPrincipalProduct;
use App\Models\Relations\Product\Mapping\ProductBuyPrice\ProductBuyPrice;
use App\Models\Relations\Product\Mapping\ProductPackaging\MappingBasePackagingUnit;
use App\Models\Relations\Product\Mapping\ProductPrincipal\ProductPrincipal;
use App\Models\Master\DiscPrincipalProduct\DiscPrincipalProduct;
use App\Models\Relations\Product\Master\Product\Product;
use Carbon\Carbon;
use Eskalink\BeCoreBase\Repositories\Core\BaseRepository;
use Eskalink\BeCoreBase\Repositories\Logs\MasterLogRepository;
use Eskalink\BeCoreBase\Services\Core\BaseService;
use Illuminate\Support\Facades\DB;

class PurchaseOrderProductListService extends BaseService
{
    protected $MasterLogRepository;
    private $baseRepository;
    private $config = [

        "tableFields" => [
            'code' => [
                "isRemap" => false,
            ],
            'shortdesc' => [
                "isRemap" => false,
            ],
            'fulldesc' => [
                "isRemap" => false,
            ],
            'is_active' => [
                "isRemap" => false,
            ],
        ]
    ];

    function __construct()
    {
        $this->baseRepository = new BaseRepository(new Product(), $this->config);
        $this->MasterLogRepository = new MasterLogRepository();
    }

    public function index($request)
    {
        return $this->executeIndex(function () use ($request) {
            $products = $this->baseRepository->index($request, function ($query) use ($request) {
                $productBuyPrice = ProductBuyPrice::where('eff_date', '<=', DB::raw("'".Carbon::now()->format("Y-m-d")."'"))
                    ->where("deleted_at", "is", DB::raw("null"));
                
                $productPrincipal = ProductPrincipal::where("princ_id", "=", DB::raw("'".$request['princ_id']."'"))
                    ->where("eff_date", "<=", DB::raw("'".Carbon::now()->format("Y-m-d")."'"))
                    ->where("deleted_at", "is", DB::raw("null"));

                return $query->withExpression("buyprice", $productBuyPrice)
                    ->withExpression("principal", $productPrincipal)
                    ->join("buyprice", 'buyprice.prd_id', '=', 'proste.id')
                    ->join("principal", 'principal.prd_id', '=', 'proste.id')
                    ->select("proste.*")->distinct(["proste.id"])
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
                    ->where('princ_id', DB::raw("'".$request['princ_id']."'"))
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

            return $products;
        });
    }
}
