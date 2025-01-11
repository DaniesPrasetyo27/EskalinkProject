<?php

namespace App\Models\Transaction\PurchaseOrder;

use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Relations\Product\Master\Product\Product;

class PurchaseOrderDiscountDocEntry extends MasterModel
{
    protected $table = 'puransordentdiscdoc';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'prd_id',
        'prd_seq',
        'disc_rate_pct',
        'disc_rate_val',
        'disc_rate_amt',
        'disc_in_val',
        'tot_disc_amt',
        'created_by',
        'created_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $relationsColumn = [
        'parent_doc_id' => [
            'table' => 'puransordentdoc',
            'relations' => 'puransordentdoc',
        ],
        'prd_id' => [
            'table' => 'proste',
            'corresponding_fields' => ['code', 'shortdesc','fulldesc'],
            'relations' => 'product',
        ]
    ];

    
    public function puransordentdoc()
    {
        return $this->belongsTo(PurchaseOrderEntry::class, 'parent_doc_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'prd_id', 'id');
    }

}
