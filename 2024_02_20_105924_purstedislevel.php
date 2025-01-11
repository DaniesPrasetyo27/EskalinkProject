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
        Schema::create('purstedislevel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('level_seq')->unique();
            $table->string('level_name',50);
            $table->string('base_disc',1)->default('N');
            $table->boolean('alw_edit_entry')->nullable()->default(false);
            $table->boolean('alw_edit_invm')->nullable()->default(false);
            $table->boolean('alw_for_order')->nullable()->default(true);
            $table->boolean('alw_for_return')->nullable()->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purstedislevel');
    }
};
