<?php

namespace App\Models\Master;

use App\Models\Relations\Warehouse\WarehouseType;
use Eskalink\BeCoreBase\Models\Base\MasterModel;

class PurchaseOrderEntryType extends MasterModel
{
    protected $table = 'pursteenttype';
    protected $compositeKeys = [
        'code',
        'is_active',
    ];
    protected $fillable = [
        'description',
        'type',
        'allow_bonusprd',
        'allow_gimmicks',
        'allow_edit_disc',
        'is_itface_ap',
        'shipto_whtype_id',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];
    protected $relationsColumn = [];

    public function warehousetype()
    {
        return $this->belongsTo(WarehouseType::class, 'shipto_whtype_id', 'id')
            ->select('id', 'code', 'description');
    }
}
