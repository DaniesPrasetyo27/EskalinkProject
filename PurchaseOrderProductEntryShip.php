<?php

namespace App\Models\Transaction\PurchaseOrder;

use App\Models\Master\PurchaseOrderEntryType;
use Eskalink\BeCoreBase\Models\Base\MasterModel;
use App\Models\Relations\Borste\BorsteLocationDetail;
use App\Models\Relations\Borste\City;
use App\Models\Relations\Borste\Country;
use App\Models\Relations\Borste\District;
use App\Models\Relations\Customer\MappingCustomerDeliveryAddress;
use App\Models\Relations\Borste\Province;
use App\Models\Relations\Borste\SubDistrict;
use App\Models\Relations\Customer\CustomerProfile;
use App\Models\Relations\Warehouse\WarehouseMaster;

class PurchaseOrderProductEntryShip extends MasterModel
{
    protected $table = 'puransordentship';
    protected $compositeKeys = [
    ];
    protected $fillable = [
        'parent_doc_id',
        'ship_to_flag',
        'wh_dest_id',
        'wh_code',
        'wh_shortname',
        'wh_fullname',
        'wh_loc_id',
        'wh_address1',
        'wh_address2',
        'wh_address3',
        'wh_country_id',
        'wh_prov_id',
        'wh_city_id',
        'wh_dist_id',
        'wh_sdist_id',
        'wh_zip_code',
        'cust_id',
        'cs_code',
        'cs_shortname',
        'cs_fullname',
        'cust_addr_id',
        'cs_addr_name',
        'cs_address1',
        'cs_address2',
        'cs_address3',
        'cs_country_id',
        'cs_prov_id',
        'cs_city_id',
        'cs_dist_id',
        'cs_sdist_id',
        'cs_zip_code',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];

    public function puransordentdoc()
    {
        return $this->belongsTo(PurchaseOrderEntryType::class, 'parent_doc_id', 'id')
            ->select('id');
    }

    public function ivtstewarehouse()
    {
        return $this->belongsTo(WarehouseMaster::class, 'wh_dest_id', 'id');
    }

    public function borstelocd()
    {
        return $this->belongsTo(BorsteLocationDetail::class, 'wh_loc_id', 'id');
    }

    public function whcountry()
    {
        return $this->belongsTo(Country::class, 'wh_country_id', 'id');
    }

    public function whprovince()
    {
        return $this->belongsTo(Province::class, 'wh_prov_id', 'id');
    }

    public function whcity()
    {
        return $this->belongsTo(City::class, 'wh_city_id', 'id');
    }
    
    public function whdistrict()
    {
        return $this->belongsTo(District::class, 'wh_dist_id', 'id');
    }

    public function whsubdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'wh_sdist_id', 'id');
    }

    public function whzipsubdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'wh_zip_code', 'zip_code');
    }

    
    public function cussteprofile()
    {
        return $this->belongsto(CustomerProfile::class, 'cust_id', 'id');
    }
    
    public function cusppiprodeladdress()
    {
        return $this->belongsto(MappingCustomerDeliveryAddress::class, 'cust_addr_id', 'id');
    }
    
    public function cscountry()
    {
        return $this->belongsto(Country::class, 'cs_country_id', 'id');
    }
    
    public function csprovince()
    {
        return $this->belongsto(Province::class, 'cs_prov_id', 'id');
    }
    
    public function cscity()
    {
        return $this->belongsto(City::class, 'cs_city_id', 'id');
    }
    
    public function csditrict()
    {
        return $this->belongsto(District::class, 'cs_dist_id', 'id');
    }
    
    public function cssubdistrict()
    {
        return $this->belongsto(SubDistrict::class, 'cs_sdist_id', 'id');
    }

    public function cszipsubdistrict()
    {
        return $this->belongsto(SubDistrict::class, 'cs_zip_code', 'zip_code');
    }
}
