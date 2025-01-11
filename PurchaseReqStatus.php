<?php

namespace App\Models\Master;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class PurchaseReqStatus extends MasterModel
{
    protected $table = 'purstereqsts';
    protected $compositeKeys = [
        'code',
    ];
    protected $fillable = [
        'shortdesc',
        'seq',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];
    protected $relationsColumn = [];
}
