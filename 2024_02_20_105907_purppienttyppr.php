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
        Schema::create('purppienttyppr', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ent_type_id')->nullable();
            $table->uuid('princ_id')->nullable();
            $table->date('eff_date');
            
            $table->foreign('ent_type_id')->references('id')->on('pursteenttype')->onDelete('cascade');
            $table->foreign('princ_id')->references('id')->on('pristeprofile')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purppienttyppr');
    }
};
