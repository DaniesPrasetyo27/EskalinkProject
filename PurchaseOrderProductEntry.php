<?php

namespace App\Models\Transaction\PurchaseOrder;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class PurchaseOrderProductEntry extends MasterModel
{
    protected $table = 'puransordentprd';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'prd_id',
        'prd_code',
        'prd_shortdesc',
        'prd_fulldesc',
        'prd_seq',
        'batch_id',
        'batch_code',
        'batch_exp_date',
        'batch_mfc_date',
        'batch_srno',
        'batch_seq',
        'pkg_id',
        'pkg_code',
        'pkg_shortdesc',
        'pkg_fulldesc',
        'pkg_seq',
        'qty',
        'basepkg_id',
        'basepkg_code',
        'basepkg_shortdesc',
        'basepkg_fulldesc',
        'convpkg',
        'baseqty',
        'prc_id',
        'prc_code',
        'prc_shortdesc',
        'prc_fulldesc',
        'prc_price_id',
        'prc_price_amt',
        'gross_amt',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
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
        ],
        'batch_id' => [
            'table' => 'prostebatch',
            'corresponding_fields' => ['code', 'exp_date','mfc_date','srno'],
            'relations' => 'prostebatch',
        ],
        'pkg_id' => [
            'table' => 'prosteunipackaging',
            'corresponding_fields' => ['code', 'shortdesc','fulldesc'],
            'relations' => 'unitpackaging',
        ],
        'basepkg_id' => [
            'table' => 'prosteunipackaging',
            'corresponding_fields' => ['code', 'shortdesc','fulldesc'],
            'relations' => 'basepackaging',
        ],
        'prc_id' => [
            'table' => 'prostebuyprigroup',
            'corresponding_fields' => ['code', 'shortdesc','fulldesc'],
            'relations' => 'prostebuyprigroup',
        ],
        'prc_price_amt' => [
            'table' => 'proppibuyprice',
            'corresponding_fields' => ['price_amt'],
            'relations' => 'proppibuyprice',
        ],
        
    ];

}
