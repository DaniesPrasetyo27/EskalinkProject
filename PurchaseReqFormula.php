<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PurchaseReqFormula extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $table = 'purstereqfrmu';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
        'pr_type_id',
        'is_active',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $relationsColumn = [
        'pr_type_id' => [
            'table' => 'pursterequistype',
            'relations' => 'purchaseReqType',
        ],
        'parm_als' => [
            'table' => 'purstereqfrmuparam',
            'relations' => 'parameters',
        ],
        'parm_src' => [
            'table' => 'purstereqfrmuparam',
            'relations' => 'parameters',
        ],
        'parm_rumus' => [
            'table' => 'purstereqfrmurmus',
            'relations' => 'formula',
        ]
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->code = strtoupper($model->code);
            $model->description = strtoupper($model->description);
        });
    }

    public function purchaseReqType()
    {
        return $this->belongsTo(PurchaseReqType::class, 'pr_type_id', 'id')
            ->select('id', 'code', 'description');
    }

    public function parameters()
    {
        return $this->hasMany(ParametersFormula::class, 'pr_frml_id', 'id');
    }

    public function formula()
    {
        return $this->hasOne(Formula::class, 'pr_parm_id', 'id');
    }

    public function scopeFilterByParams(Builder $query, array $params): Builder
    {
        return $query->where(function ($query) use ($params) {
            foreach ($params as $item) {
                if (!empty($item['field'])) {
                    $item['value'] = ($item['value'] === 'true') ? '1' : ($item['value'] === 'false' ? '0' : $item['value']);
                    if ($item['field'] === 'parm_als' || $item['field'] === 'parm_src') {
                        $query->whereHas('parameters', function ($query) use ($item) {
                            $this->operand($query, $item);
                        });
                    } else if ($item['field'] === 'parm_rumus') {
                        $query->whereHas('formula', function ($query) use ($item) {
                            $this->operand($query, $item);
                        });
                    } else if($item['field'] === 'pr_type_id') {
                        $query->whereHas('purchaseReqType', function ($query) use ($item) {
                            if($item['operand'] === 'is in list') {
                                $value = explode(',', $item['value'][0]);
                                $query->whereIn('code', $value);
                                $query->orWhereIn('description', $value);
                            } else if($item['operand'] === 'is not in list') {
                                $value = explode(',', $item['value'][0]);
                                $query->whereNotIn('code', $value);
                                $query->orWhereNotIn('description', $value);
                            } else if($item['operand'] === 'like' || $item['operand'] === 'not like') {
                                $value = $item['value'][0];
                                $query->where('code', $item['operand'], "%$value%");
                                $query->orWhere('description', $item['operand'], "%$value%");
                            } else if($item['operand'] === 'between') {
                                $startBetween = $item['value'][0];
                                $endBetween = $item['value'][1];
                                if (!empty($startBetween) && !empty($endBetween)) {
                                    $query->whereBetween($item['field'], [$startBetween, $endBetween]);
                                } elseif (!empty($startBetween)) {
                                    $query->where($item['field'], '>=', $startBetween);
                                } elseif (!empty($endBetween)) {
                                    $query->where($item['field'], '<=', $endBetween);
                                }
                            } else {
                                $query->where('code', $item['operand'], $item['value'][0]);
                                $query->orWhere('description', $item['operand'], $item['value'][0]);
                            }
                        });
                    } else {
                        $query->where(function ($query) use ($item) {
                            $this->operand($query, $item);
                        });
                    }
                }
            }
        });       
    }

    public function scopeKeywordSearch(Builder $query, string $searchKeyword, array $searchFields): Builder
    {
        if(count($searchFields) < 1){
            $searchFields = array_merge($this->fillable, array_keys($this->relationsColumn));
        }
        return $query->where(function ($innerQuery) use ($searchKeyword, $searchFields) {
            foreach ($searchFields as $column) {
                if(isset($this->relationsColumn[$column])){
                    $relationName = $this->relationsColumn[$column]['relations'];
                    $innerQuery->orWhereHas($relationName, function($relationQuery) use ($column, $searchKeyword){
                        $relationQuery->where($column, 'like', "%{$searchKeyword}%");
                    });
                } else {
                    $innerQuery->orWhere($column, 'like', "%{$searchKeyword}%");
                }
            }
        });
    }

    public function operand($query, $item)
    {
        if($item['operand'] == 'is in list') {
            $query->whereIn($item['field'], $item['value']);
        } else if($item['operand'] == 'is not in list') {
            $query->whereNotIn($item['field'], $item['value']);
        } else if($item['operand'] == 'like' || $item['operand'] == 'not like') {
            $value = $item['value'][0];
            $query->where($item['field'], $item['operand'], "%$value%");
        } else if($item['operand'] == 'between') {
            $startBetween = $item['value'][0];
            $endBetween = $item['value'][1];
            if (!empty($startBetween) && !empty($endBetween)) {
                $query->whereBetween($item['field'], [$startBetween, $endBetween]);
            } elseif (!empty($startBetween)) {
                $query->where($item['field'], '>=', $startBetween);
            } elseif (!empty($endBetween)) {
                $query->where($item['field'], '<=', $endBetween);
            }
        } else {
            $query->where($item['field'], $item['operand'], $item['value'][0]);
        }
    }
}