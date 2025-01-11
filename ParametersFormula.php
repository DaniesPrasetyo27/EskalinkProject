<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ParametersFormula extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $table = 'purstereqfrmuparam';
    public $timestamps = false;

    protected $fillable = [
        'pr_frml_id',
        'parm_als',
        'parm_src',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->parm_als = strtoupper($model->parm_als);
        });
    }

    public function purchaseReqFormula()
    {
        return $this->belongsTo(PurchaseReqFormula::class, 'pr_frml_id', 'id');
    }
}
