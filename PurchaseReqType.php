<?php

namespace App\Models\Master;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class PurchaseReqType extends MasterModel
{
    protected $table = 'pursterequistype';
    protected $compositeKeys = [
        'code',
        'is_active',
    ];
    protected $fillable = [
        'description',
        'type',
        'appr_due_day',
        'split_top_prd',
        'split_timely_paym',
        'std_uom',
        'allow_chg_uom',
        'auto_gen_prd',
        'allow_chg_prd',
        'allow_add_qty',
        'allow_red_qty',
        'mand_sono_ref',
        'mand_prno_ref',
        'mand_reason',
        'is_itface_po',
        'def_pobr_eq_prbr',
        'def_podt_eq_prdt',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];
}
