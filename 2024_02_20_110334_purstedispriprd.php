<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
 /**    BELOM STABLE//
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purstedispriprd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('princ_id');
            $table->uuid('prd_id');
            $table->uuid('disc_id');
            $table->integer('disc_lv');
            $table->float('def_disc_pct',20.4)->default('0');
            $table->float('def_disc_val',20.4)->default('0');
            $table->date('eff_date');
            
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();  
            
            $table->foreign('princ_id')->references('id')->on('pristeprofile')->onDelete('cascade');
            $table->foreign('prd_id')->references('id')->on('proste')->onDelete('cascade');
            $table->foreign('disc_id')->references('id')->on('purstedislevel')->onDelete('cascade');
            $table->foreign('disc_lv')->references('level_seq')->on('purstedislevel'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purstedispriprd');
    }
};
