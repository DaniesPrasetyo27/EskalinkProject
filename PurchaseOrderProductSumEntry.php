<?php

namespace App\Models\Transaction\PurchaseOrder;

use App\Models\Master\PurchaseOrderEntryType;
use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Relations\Product\Master\Product\Master\Product\Product;
use App\Models\Relations\Product\Master\PackagingUnit\ProductUnit;

class PurchaseOrderProductSumEntry extends MasterModel
{
    protected $table = 'puransordentprdsum';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'prd_id',
        'prd_code',
        'prd_shortdesc',
        'prd_fulldesc',
        'prd_seq',
        'sml_pkg_id',
        'sml_estpkg_code',
        'sml_estpkg_shortdesc',
        'sml_estpkg_fulldesc',
        'sml_estqty',
        'basepkg_id',
        'basepkg_code',
        'basepkg_shortdesc',
        'basepkg_fulldesc',
        'convpkg',
        'baseqty',
        'gross_amt',
        'disc_amt',
        'dpp_amt',
        'tax_amt',
        'net_amt',
        'remark',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];
    protected $relationsColumn = [];

}
