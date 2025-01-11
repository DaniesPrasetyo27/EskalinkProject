<?php

namespace App\Models\Transaction\PurchaseOrder;

use App\Models\Master\PurchaseOrderEntryType;
use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Relations\Principal\Principal;

class PurchaseOrderEntry extends MasterModel
{
    protected $table = 'puransordentdoc';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'ent_type_id',
        'ent_type_desc',
        'princ_id',
        'princ_code',
        'princ_shortname',
        'princ_fullname',
        'doc_no',
        'doc_date',
        'doc_ref',
        'doc_remark',
        'rqs_doc_id',
        'rqs_doc_no',
        'req_delv_date',
        'prm_ship_date',
        'prm_delv_date',
        'is_active',
        'created_at',
        'created_by',
    ];

    protected $relationsColumn = [
        'ent_type_id' => [
            'table' => 'pursteenttype',
            'corresponding_fields' => ['description'],
            'relations' => 'pursteenttype'
        ],
        'princ_id' => [
            'table' => 'pristeprofile',
            'corresponding_fields' => ['code, shortname'],
            'relations' => 'pristeprofile'
        ]
    ];

    public function pursteenttype()
    {
        return $this->belongsTo(PurchaseOrderEntryType::class, 'ent_type_id', 'id');
    }

    public function pristeprofile()
    {
        return $this->belongsTo(Principal::class, 'princ_id', 'id');
    }
}
