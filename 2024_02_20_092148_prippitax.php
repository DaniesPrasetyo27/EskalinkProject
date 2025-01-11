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
        Schema::create('prippitax', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('princ_id');
            $table->string('tax_no');
            $table->string('tax_name',30);
            $table->uuid('tax_type');
            $table->boolean('tax_emplr_flag',100);
            $table->string('tax_emplr_no');
            $table->date('tax_emplr_date');
            $table->string('tax_address1',30);
            $table->string('tax_address2',50);
            $table->string('tax_address3', 100);
            $table->uuid('country_id');
            $table->uuid('prov_id',30);
            $table->uuid('city_id',50);
            $table->uuid('dist_id', 100);
            $table->uuid('sdist_id');
            $table->string('zip_code', 15);
            $table->boolean('is_active',50);
            $table->date('eff_date', 100);
            
        
            $table->foreign('princ_id')->references('id')->on('pristeprofile');
            $table->foreign('tax_type')->references('id')->on('taxstetype');
            $table->foreign('country_id')->references('id')->on('terste1country');
            $table->foreign('prov_id')->references('id')->on('terste2province');
            $table->foreign('city_id')->references('id')->on('terste3city');
            $table->foreign('dist_id')->references('id')->on('terste4district');
            $table->foreign('sdist_id')->references('id')->on('terste5subdistrict');
            $table->foreign('zip_code')->references('zip_code')->on('terste5subdistrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prippitax');
    }
};
