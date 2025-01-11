<?php

namespace App\Models\Transaction\PurchaseOrder;

use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Master\PurchaseOrdStatus;

class PurchaseOrderEntryStatus extends MasterModel
{
    protected $table = 'puransordentsts';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'doc_sts',
        'sts_shortdesc',
        'is_active',
        'doc_remark',
        'created_at',
        'created_by',
        'deleted_by',
        'deleted_at'
    ];
    protected $relationsColumn = [
        'parent_doc_id' => [
            'table' => 'puransordentdoc',
            'relations' => 'puransordentdoc',
        ],
        'doc_sts' => [
            'table' => 'purstereqsts',
            'corresponding_fields' => ['code'],
            'relations' => 'purstereqsts',
        ],
        'sts_shortdesc' => [
            'table' => 'purstereqsts',
            'corresponding_fields' => ['shortdesc'],
            'relations' => 'purstereqsts',
        ],
    ];

    public function puransordentdoc()
    {
        return $this->belongsTo(PurchaseOrderEntry::class, 'parent_doc_id', 'id');
    }

    public function purstereqsts()
    {
        return $this->hasOne(PurchaseOrdStatus::class, 'code', 'doc_sts')
            ->select('code','shortdesc');
    }
}
