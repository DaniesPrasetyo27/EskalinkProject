<?php

namespace App\Http\Requests\Transaction\PurchaseOrder;

use Illuminate\Contracts\Validation\Validator;
use Eskalink\BeCoreBase\Http\Requests\Base\BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseOrderEntryRequest extends BaseFormRequest
{
    protected $tableNames = [
        'puransordentdoc',
        'puransordentsts',
        'puransordentbill',
        'puransordentship',

    ];

    public function authorize(): bool
    {
        return true;
    }

        /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // GET TABLE RULES FROM REDIS/DB
        $tableSchemes = [];

        foreach ($this->tableNames as $key => $value) {
            $tableSchemes[$value] = $this->getTableScheme($value);
            if($tableSchemes[$value]){
                $tableSchemes[$value] = json_decode(json_encode($tableSchemes[$value]), true);
            }
        }

        // $isEditing = $this->method() === 'PUT' || $this->method() === 'PATCH';

        return [
            'ent_type_id' => [
                'required',
                'regex:/^[A-Z0-9@\/\-_]+$/',
                'uppercase'
            ],
            'ent_type_desc' => [
                'required',
            ],
            'princ_id' => ['max:36','nullable'],
            'princ_code' => ['regex:/^[a-zA-Z0-9@\/\-_]+$/', 'string', 'max:30'],
            'princ_shortname' => ['string', 'max:50'],
            'princ_fullname' => ['string', 'max:100'],
            'doc_no' => ['string', 'max:30'],
            'doc_date' => ['date'],
            'doc_ref' => ['string', 'max:100'],
            'doc_remark' => ['string', 'max:100'],
            'rqs_doc_id' => ['string', 'max:36'],
            'rqs_doc_no' => ['string', 'max:30'],
            'req_delv_date' => ['date'],
            'prm_ship_date' => ['date'],
            'prm_delv_date' => ['date'],
            'is_active' => ['boolean'],
            'created_by' => ['nullable', 'string', 'max:30'],
            'deleted_by' => ['nullable'],

            // Purchase Order Status

            'puransordentsts' => ['required'],
            'puransordentsts.doc_sts' => ['required'],
            'puransordentsts.sts_shortdesc' => ['required'],
            'puransordentsts.is_active' => ['boolean'],
            'puransordentsts.doc_remark' => ['nullable'],
            'puransordentsts.created_by' => ['string'],

            'puransordentship' => ['required'],
            'puransordentship.ship_to_flag' => ['nullable'],
            'puransordentship.wh_dest_id' => ['required'],
            'puransordentship.wh_code' => ['required'],
            'puransordentship.wh_shortname' => ['required'],
            'puransordentship.wh_fullname' => ['required'],
            'puransordentship.wh_loc_id' => ['required'],
            'puransordentship.wh_address1' => ['required'],
            'puransordentship.wh_address2' => ['string'],
            'puransordentship.wh_address3' => ['string'],
            'puransordentship.wh_country_id' => ['required'],
            'puransordentship.wh_prov_id' => ['required'],
            'puransordentship.wh_city_id' => ['required'],
            'puransordentship.wh_dist_id' => ['required'],
            'puransordentship.wh_sdist_id' => ['required'],
            'puransordentship.wh_zip_code' => ['required'],
            'puransordentship.cust_id' => ['nullable'],
            'puransordentship.cs_code' => ['nullable'],
            'puransordentship.cs_shortname' => ['nullable'],
            'puransordentship.cs_fullname' => ['nullable'],
            'puransordentship.cust_addr_id' => ['nullable'],
            'puransordentship.cs_addr_name' => ['nullable'],
            'puransordentship.cs_address1' => ['nullable'],
            'puransordentship.cs_address2' => ['nullable'],
            'puransordentship.cs_address3' => ['nullable'],
            'puransordentship.cs_country_id' => ['nullable'],
            'puransordentship.cs_prov_id' => ['nullable'],
            'puransordentship.cs_city_id' => ['nullable'],
            'puransordentship.cs_dist_id' => ['nullable'],
            'puransordentship.cs_sdist_id' => ['nullable'],
            'puransordentship.cs_zip_code' => ['nullable'],
            'puransordentship.created_by' => ['nullable', 'string', 'max:30'],

            'puransordentbill' => ['required'],
            'puransordentbill.top_id' => ['nullable'],
            'puransordentbill.top_code' => ['string'],
            'puransordentbill.top_shortdesc' => ['string'],
            'puransordentbill.top_fulldesc' => ['string'],
            'puransordentbill.top_top_days' => ['string'],
            'puransordentbill.paytp_id' => ['nullable',],
            'puransordentbill.paytp_code' => ['string',],
            'puransordentbill.paytp_shortdesc' => ['string',],
            'puransordentbill.paytp_fulldesc' => ['string',],
            'puransordentbill.curr_id' => ['nullable',],
            'puransordentbill.curr_code' => ['string',],
            'puransordentbill.curr_shortdesc' => ['string',],
            'puransordentbill.curr_fulldesc' => ['string',],
            'puransordentbill.tax_id' => ['nullable',],
            'puransordentbill.tax_code' => ['string',],
            'puransordentbill.tax_shortdesc' => ['string',],
            'puransordentbill.tax_fulldesc' => ['string',],
            'puransordentbill.tax_rate_id' => ['nullable',],
            'puransordentbill.tax_rate_val' => ['string'],
            'puransordentbill.created_by' => ['nullable', 'string', 'max:30'],

           // Transfer Entry Product
           'puransordentprdsum' => ['required', 'array'],
           'puransordentprdsum.*.prd_id'=> ['required'],
           'puransordentprdsum.*.prd_code' => ['required'],
           'puransordentprdsum.*.prd_shortdesc' => ['nullable'],
           'puransordentprdsum.*.prd_fulldesc' => ['nullable'],
           'puransordentprdsum.*.prd_seq' => ['required'],
           'puransordentprdsum.*.sml_pkg_id' => ['required'],
           'puransordentprdsum.*.sml_estpkg_code' => ['required'],
           'puransordentprdsum.*.sml_estpkg_shortdesc' => ['required'],
           'puransordentprdsum.*.sml_estpkg_fulldesc' => ['required'],
           'puransordentprdsum.*.sml_estqty' => ['required'],
           'puransordentprdsum.*.basepkg_id' => ['required'],
           'puransordentprdsum.*.basepkg_code' => ['required'],
           'puransordentprdsum.*.basepkg_shortdesc' => ['required'],
           'puransordentprdsum.*.basepkg_fulldesc' => ['required'],
           'puransordentprdsum.*.convpkg' => ['required'],
           'puransordentprdsum.*.baseqty' => ['required'],
           'puransordentprdsum.*.gross_amt' => ['required'],
           'puransordentprdsum.*.disc_amt' => ['required'],
           'puransordentprdsum.*.dpp_amt' => ['required'],
           'puransordentprdsum.*.tax_amt' => ['required'],
           'puransordentprdsum.*.net_amt' => ['required'],
           'puransordentprdsum.*.remark' => ['nullable'],
           'puransordentprdsum.*.created_at' => ['nullable'],
           'puransordentprdsum.*.puransordentprd' => ['required', 'array'],
           'puransordentprdsum.*.puransordentdiscdoc.parent_doc_id' => ['nullable'],
           'puransordentprdsum.*.puransordentdiscdoc.prd_id' => ['nullable'],
           'puransordentprdsum.*.puransordentdiscdoc.prd_seq' => ['nullable','integer'],
           'puransordentprdsum.*.puransordentdiscdoc.disc_rate_pct' => ['nullable', 'decimal:0,4'],
           'puransordentprdsum.*.puransordentdiscdoc.disc_rate_val' => ['nullable', 'decimal:0,4'],
           'puransordentprdsum.*.puransordentdiscdoc.disc_rate_amt' => ['nullable', 'decimal:0,4'],
           'puransordentprdsum.*.puransordentdiscdoc.disc_in_val' => ['nullable', 'decimal:0,4'],
           'puransordentprdsum.*.puransordentdiscdoc.tot_disc_amt' => ['nullable', 'decimal:0,4'],
           'puransordentprdsum.*.puransordentdiscprd' => ['nullable'],

        ];
    }

    public function attributes()
    {
        return [
            'ent_type_id' => 'Entry Type ID',
            'ent_type_desc' => 'Entry Type Desc',
            'princ_id' => 'Principal ID',
            'princ_code' => 'Principal code',
            'princ_shortname' => 'Principal shortname',
            'princ_fullname' => 'Principal fullname',
            'doc_no' => 'document number',
            'doc_date' => 'document date',
            'doc_ref' => 'document referensi',
            'doc_remark' => 'document remark',
            'rqs_doc_id' => 'requisition ID',
            'rqs_doc_no' => 'requisition Doc No',
            'req_delv_date' => 'Request Delivery Date',
            'prm_ship_date' => 'Promise Ship Date',
            'prm_delv_date' => 'Promise Delivery Date',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        $remainingErrorsCount = $validator->errors()->count() - 1;

        $response = response()->json([
            'status' => 422,
            'message' => $firstError . ($remainingErrorsCount > 0 ? " and $remainingErrorsCount more field(s)" : ""),
            'errors' => $validator->errors()
        ], 422);

        throw new HttpResponseException($response);
    }
}
