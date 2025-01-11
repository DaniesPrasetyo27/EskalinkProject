<?php

namespace App\Models\Transaction\PurchaseOrder;

use App\Models\Master\DiscountLevel;
use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Master\DiscPrincipalProductMaster;
use App\Models\Relations\Product\Master\Product\Product;

class PurchaseOrderDiscountPrdEntry extends MasterModel
{
    protected $table = 'puransordentdiscdoc';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'prd_id',
        'prd_Seq',
        'level_id',
        'level_seq',
        'level_name',
        'base_disc',
        'bef_disc_amt',
        'disc_id',
        'disc_rate_pct',
        'disc_rate_val',
        'disc_rate_amt',
        'disc_in_val',
        'tot_disc_amt',
        'aft_disc_amt',
        'created_at',
        'deleted_by',
        'deleted_at',
    ];

    
    public function puransordentdoc()
    {
        return $this->belongsTo(PurchaseOrderEntry::class, 'parent_doc_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'prd_id', 'id');
    }

    public function purstedislevel()
    {
        return $this->belongsTo(DiscountLevel::class, 'level_id', 'id');
    }

    public function purstedispriprd()
    {
        return $this->belongsTo(DiscPrincipalProductMaster::class, 'disc_id', 'id');
    }

}
