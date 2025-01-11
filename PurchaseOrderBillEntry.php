<?php

namespace App\Models\Transaction\PurchaseOrder;

use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Relations\Currency\MasterCurrency;
use App\Models\Relations\TermPayment\Top;
use App\Models\Relations\TermPayment\TopType;
use App\Models\Relations\Tax\MasterTax;
use App\Models\Relations\Tax\MasterTaxDetail;

class PurchaseOrderBillEntry extends MasterModel
{
    protected $table = 'puransordentbill';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'top_id',
        'top_code',
        'top_shortdesc',
        'top_fulldesc',
        'top_top_days',
        'paytp_id',
        'paytp_code',
        'paytp_shortdesc',
        'paytp_fulldesc',
        'curr_id',
        'curr_code',
        'curr_shortdesc',
        'curr_fulldesc',
        'tax_id',
        'tax_code',
        'tax_shortdesc',
        'tax_fulldesc',
        'tax_rate_id',
        'tax_rate_val',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];

    
    public function puransordentdoc()
    {
        return $this->belongsTo(PurchaseOrderEntry::class, 'parent_doc_id', 'id');
    }

    public function tpystetop()
    {
        return $this->belongsTo(Top::class, 'top_id', 'id');
    }

    public function tpystetype()
    {
        return $this->belongsTo(TopType::class, 'paytp_id', 'id');
    }

    public function curste()
    {
        return $this->belongsTo(MasterCurrency::class, 'curr_id', 'id');
    }

    public function taxsteh()
    {
        return $this->belongsTo(MasterTax::class, 'tax_id', 'id');
    }

    public function taxsted()
    {
        return $this->belongsTo(MasterTaxDetail::class, 'tax_rate_id', 'id');
    }

}
