<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('puransordentship', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->string('ship_to_flag',1);
            $table->uuid('wh_dest_id');
            $table->string('wh_code',30);
            $table->string('wh_shortname',50);
            $table->string('wh_fullname',100)->nullable();
            $table->uuid('wh_loc_id');
            $table->string('wh_address1',100);
            $table->string('wh_address2',100)->nullable();
            $table->string('wh_address3',100)->nullable();
            $table->uuid('wh_country_id');
            $table->uuid('wh_prov_id');
            $table->uuid('wh_city_id');
            $table->uuid('wh_dist_id');
            $table->uuid('wh_sdist_id');
            $table->string('wh_zip_code',15);
            $table->uuid('cust_id',30);
            $table->string('cs_code',50);
            $table->string('cs_shortname', 50);
            $table->string('cs_fullname', 100)->nullable();
            $table->uuid('cust_addr_id');
            $table->string('cs_addr_name',100);
            $table->string('cs_address1',100);
            $table->string('cs_address2',100)->nullable();
            $table->string('cs_address3',100)->nullable();
            $table->uuid('cs_country_id');
            $table->uuid('cs_prov_id');
            $table->uuid('cs_city_id');
            $table->uuid('cs_dist_id');
            $table->uuid('cs_sdist_id');
            $table->string('cs_zip_code',15);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();  
        
            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc');
            $table->foreign('wh_dest_id')->references('id')->on('ivtstewarehouse');
            $table->foreign('wh_loc_id')->references('id')->on('borstelocd');
            $table->foreign('wh_country_id')->references('id')->on('terste1country');
            $table->foreign('wh_prov_id')->references('id')->on('terste2province');
            $table->foreign('wh_city_id')->references('id')->on('terste3city');
            $table->foreign('wh_dist_id')->references('id')->on('terste4district');
            $table->foreign('wh_sdist_id')->references('id')->on('terste5subdistrict');
            $table->foreign('wh_zip_code')->references('zip_code')->on('terste5subdistrict');
            $table->foreign('cust_id')->references('id')->on('cussteprofile');
            $table->foreign('cust_addr_id')->references('id')->on('cusppiprodeladdress');
            $table->foreign('cs_country_id')->references('id')->on('terste1country');
            $table->foreign('cs_prov_id')->references('id')->on('terste2province');
            $table->foreign('cs_city_id')->references('id')->on('terste3city');
            $table->foreign('cs_dist_id')->references('id')->on('terste4district');
            $table->foreign('cs_sdist_id')->references('id')->on('terste5subdistrict');
            $table->foreign('cs_zip_code')->references('zip_code')->on('terste5subdistrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentship');
    }
};
