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
        Schema::create('pursteordrecsts', function (Blueprint $table) {
            $table->string('code',2)->primary();
            $table->string('shortdesc',50);
            $table->integer('seq');
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
        Schema::dropIfExists('pursteordrecsts');
    }
};
