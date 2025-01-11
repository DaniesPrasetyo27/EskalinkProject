<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puransordappusr', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_stat_id');
            $table->integer('seq_appr');
            $table->uuid('empl_lv_id');
            $table->string('empl_lv_code',30);
            $table->string('empl_lv_shortdesc',50)->nullable();
            $table->string('empl_lv_fulldesc',100)->nullable();
            $table->uuid('empl_id')->nullable();
            $table->string('empl_code',30)->nullable();
            $table->string('empl_shortname',50)->nullable();
            $table->string('empl_fullname',100)->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('user_name',255)->nullable();
            $table->string('user_username',20)->nullable(); 
            
            $table->timestamp('approved_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));   
            $table->string('approved_by', 30);  
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
        
            $table->foreign('parent_stat_id')->references('id')->on('puransordentsts');
            $table->foreign('empl_lv_id')->references('id')->on('empstelevel');
            $table->foreign('empl_id')->references('id')->on('empsteprofile');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordappusr');
    }
};