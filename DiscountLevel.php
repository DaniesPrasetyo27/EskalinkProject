<?php

namespace App\Models\Master;

use Eskalink\BeCoreBase\Models\Base\MasterModel;

class DiscountLevel extends MasterModel
{
    protected $table = 'purstedislevel';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'level_seq',
        'level_name',
        'base_disc',
        'alw_edit_entry',
        'alw_edit_invm',
        'alw_for_order',
        'alw_for_return',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $relationsColumn = [];
}
