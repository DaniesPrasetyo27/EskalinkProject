<?php

namespace App\Models\Master;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class Formula extends MasterModel
{
    protected $table = 'purstereqfrmurmus';
    protected $compositeKeys = [
        'pr_parm_id',
    ];
    protected $fillable = [
        'parm_rumus',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];
    
    protected $relationsColumn = [];

    public function purchaseReqFormula()
    {
        return $this->belongsTo(PurchaseReqFormula::class, 'pr_parm_id', 'id');
    }
}
