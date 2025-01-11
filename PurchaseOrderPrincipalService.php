<?php

namespace App\Services\Transaction\PurchaseOrder;

use App\Models\Relations\Principal\Principal;
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

class PurchaseOrderPrincipalService extends BaseService
{
    protected $MasterLogRepository;
    protected $baseRepository;
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
        $this->baseRepository = new BaseRepository(new Principal(), $this->config);
        $this->MasterLogRepository = new MasterLogRepository();
    }

    public function index($request)
    {
        return $this->executeIndex(function () use ($request) {
            $principal = $this->baseRepository->index($request, function($query) use ($request) {
                $webTrf =  DB::table('webppiprincipal')->where('user_id', '=', $request['user_id'])
                ->join('pristeprofile', 'pristeprofile.id', '=', 'webppiprincipal.princ_id')
                ->where('webppiprincipal.eff_date', '<=', now()->format("Y-m-d"))
                ->whereNull('webppiprincipal.deleted_at')
                ->select(
                    'webppiprincipal.id',
                    'webppiprincipal.princ_id',
                    'webppiprincipal.user_id'
                );

                $users =  DB::table('users')->where('user_id', '=', $request['user_id'])
                ->join('cte_princuser', 'cte_princuser.user_id', '=', 'users.id')
                ->select(
                    'users.*'
                );

                $top =  DB::table('prippitop')->join('cte_princuser', 'prippitop.princ_id', '=', 'cte_princuser.princ_id')
                ->join('tpystetop', 'prippitop.top_id', '=', 'tpystetop.id')
                ->where('prippitop.eff_date', '<=', now()->format("Y-m-d"))
                ->whereNull('prippitop.deleted_at')
                ->where('tpystetop.is_active', '=', 1)
                ->whereNull('tpystetop.deleted_at')
                ->select(
                    'prippitop.id',
                    'prippitop.top_id',
                    'prippitop.princ_id',
                    'tpystetop.code as top_code',
                    'tpystetop.shortdesc as top_shortdesc',
                    'tpystetop.fulldesc as top_fulldesc',
                    'tpystetop.top_days as top_top_days',
                    'tpystetop.is_active'
                );
                
                $toptermpay =  DB::table('tpyppitoptype')->join('cte_top', 'tpyppitoptype.top_id', '=', 'cte_top.top_id')
                ->join('tpystetype', 'tpyppitoptype.paytp_id', '=', 'tpystetype.id')
                ->where('tpyppitoptype.eff_date', '<=', now()->format("Y-m-d"))
                // ->whereNull('tpyppitoptype.deleted_at')
                ->where('tpystetype.is_active', '=', 1)
                ->whereNull('tpystetype.deleted_at')
                ->select(
                    'tpyppitoptype.id',
                    'tpyppitoptype.paytp_id',
                    'tpyppitoptype.top_id',
                    'tpystetype.code as paytp_code',
                    'tpystetype.shortdesc as paytp_shortdesc',
                    'tpystetype.fulldesc as paytp_fulldesc',
                    'tpystetype.is_active'
                );


                $curr =  DB::table('prippicurrency')->join('cte_princuser', 'prippicurrency.princ_id', '=', 'cte_princuser.princ_id')
                ->join('curste', 'prippicurrency.curr_id', '=', 'curste.id')
                ->where('prippicurrency.eff_date', '<=', now()->format("Y-m-d"))
                ->whereNull('prippicurrency.deleted_at')
                ->where('curste.is_active', '=', 1)
                ->whereNull('curste.deleted_at')
                ->select(
                    'prippicurrency.id',
                    'prippicurrency.curr_id',
                    'prippicurrency.princ_id',
                    'curste.code as curr_code',
                    'curste.shortdesc as curr_shortdesc',
                    'curste.fulldesc as curr_fulldesc',
                    'curste.curr_symbol as curr_symbol',
                    'curste.is_active'
                );

                return $query
                ->withExpression("cte_princuser", $webTrf)
                ->withExpression("cte_users", $users)
                ->withExpression("cte_top", $top)
                ->withExpression("cte_toptermpay", $toptermpay)
                ->withExpression("cte_curr", $curr)
                ->join('cte_princuser', 'cte_princuser.princ_id', '=', 'pristeprofile.id')
                ->join('cte_top', 'cte_top.princ_id', '=', 'pristeprofile.id')
                ->join('cte_toptermpay', 'cte_toptermpay.top_id', '=', 'cte_top.top_id')
                ->join('cte_curr', 'cte_curr.princ_id', '=', 'pristeprofile.id')
                ->select(
                    'cte_princuser.*',
                    'cte_top.*',
                    'cte_toptermpay.*',
                    // 'cte_tax.*',
                    'cte_curr.*',
                    'pristeprofile.code',
                    'pristeprofile.shortname',
                    'pristeprofile.fullname',
                    'pristeprofile.created_at'  // Tambahkan kolom ini
                )
                ->distinct();
            });

            return $principal;
        });
    }
}
