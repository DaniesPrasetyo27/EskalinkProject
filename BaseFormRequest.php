<?php

namespace App\Http\Requests\Base;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Repositories\TableSchemeRepository;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private $tableSchemeRepository;

    public $numericTypes = [
        'bigint',
        'numeric',
        'bit',
        'smallint',
        'decimal',
        'smallmoney',
        'int',
        'tinyint',
        'money',
        'float',
        'real',
    ];

    public $stringTypes = [
        'char',
        'varchar',
        'text',
        'nchar',
        'nvarchar',
        'ntext',
    ];

    public $fileTypes = [
        'binary',
        'varbinary',
        'image',
    ];

    public $dateTypes = [
        'date',
        'datetime2',
        'smalldatetime',
        'datetime'
    ];

    public $timeTypes = [
        'time'
    ];

    public $timezoneTypes = [
        'datetimeoffset',
    ];

    public function __construct()
    {
        $this->tableSchemeRepository = new TableSchemeRepository();
    }

    public function getTableScheme($tableName)
    {
        $prefix = 'table_scheme:' . $tableName;

        if(Redis::exists($prefix)) {
            $redis = json_decode(Redis::get($prefix));

            $result = [];

            foreach ($redis as $key => $value) {
                $type = $this->setDataTypeRule($value->DATA_TYPE);

                $result[$value->COLUMN_NAME] = [
                    'table_name' => $value->TABLE_NAME ?? '',
                    'data_type' => $type,
                    'character_maximum_length' => $value->CHARACTER_MAXIMUM_LENGTH ?? '',
                ];
            }

            return $result;
        } else {
            $tableScheme = $this->tableSchemeRepository->show($tableName);

            $result = [];

            foreach ($tableScheme as $key => $value) {
                $type = $this->setDataTypeRule($value->DATA_TYPE);

                $result[$value->COLUMN_NAME] = [
                    'table_name' => $value->TABLE_NAME ?? '',
                    'data_type' => $type,
                    'character_maximum_length' => $value->CHARACTER_MAXIMUM_LENGTH ?? '',
                ];
            }

            Redis::set($prefix, json_encode($tableScheme));

            return json_decode(json_encode($result), true);
        }

        return [];
    }

    public function setDataTypeRule($type)
    {
        if(in_array($type, $this->numericTypes)) {
            return 'numeric';
        } elseif(in_array($type, $this->stringTypes)) {
            return 'string';
        } elseif(in_array($type, $this->dateTypes)) {
            return 'date';
        } elseif(in_array($type, $this->timezoneTypes)) {
            return 'timezone';
        } elseif(in_array($type, $this->timeTypes)) {
            return 'date_format:H:i:s';
        } elseif(in_array($type, $this->fileTypes)) {
            return 'file';
        }

        return '';
    }
}
