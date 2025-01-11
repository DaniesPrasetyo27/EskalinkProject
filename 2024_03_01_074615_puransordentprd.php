<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puransordentprd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('prd_id');
            $table->string('prd_code',30);
            $table->string('prd_shortdesc',50);
            $table->string('prd_fulldesc',100);
            $table->integer('prd_seq');
            $table->uuid('batch_id');
            $table->string('batch_code',30);
            $table->date('batch_exp_date');
            $table->date('batch_mfc_date');
            $table->string('batch_srno', 30);
            $table->integer('batch_seq',);
            $table->uuid('pkg_id');
            $table->string('pkg_code',30);
            $table->string('pkg_shortdesc',50);
            $table->string('pkg_fulldesc',100);
            $table->integer('pkg_seq',);
            $table->float('qty',20.4);
            
            $table->uuid('basepkg_id');
            $table->string('basepkg_code',30);
            $table->string('basepkg_shortdesc', 50);
            $table->string('basepkg_fulldesc', 100);
            $table->float('convpkg',20.4);
            $table->float('baseqty',20.4);
            $table->uuid('prc_id');
            $table->string('prc_code',30);
            $table->string('prc_shortdesc',50);
            $table->string('prc_fulldesc', 100);
            $table->uuid('prc_price_id');
            $table->float('prc_price_amt',20.4);
            $table->float('gross_amt',20.4);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        
            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc');
            $table->foreign('prd_id')->references('id')->on('proste');
            $table->foreign('batch_id')->references('id')->on('prostebatch');
            $table->foreign('pkg_id')->references('id')->on('prosteunipackaging');
            $table->foreign('basepkg_id')->references('id')->on('prosteunipackaging');
            $table->foreign('prc_id')->references('id')->on('prostebuyprigroup');
            $table->foreign('prc_price_id')->references('id')->on('proppibuyprice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentprd');
    }
};
