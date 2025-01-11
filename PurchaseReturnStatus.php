<?php

namespace App\Models\Master;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class PurchaseReturnStatus extends MasterModel
{
    protected $table = 'pursteretsts';
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
