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
        Schema::create('cusppiprodeladdress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cust_id');
            $table->string('addr_name', 100);
            $table->string('address1', 100);
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->uuid('country_id');
            $table->uuid('prov_id');
            $table->uuid('city_id');
            $table->uuid('dist_id');
            $table->uuid('sdist_id');
            $table->string('zip_code',15);
            $table->integer('lead_time_day');
            $table->string('win_delv_time',5);
            $table->boolean('is_active')->default(true);
            $table->date('eff_date');
            
            $table->foreign('cust_id')->references('id')->on('cussteprofile')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('terste1country')->onDelete('cascade');
            $table->foreign('prov_id')->references('id')->on('terste2province')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('terste3city')->onDelete('cascade');
            $table->foreign('dist_id')->references('id')->on('terste4district')->onDelete('cascade');
            $table->foreign('sdist_id')->references('id')->on('terste5subdistrict')->onDelete('cascade');
            $table->foreign('zip_code')->references('zip_code')->on('terste5subdistrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cusppiprodeladdress');
    }
};
