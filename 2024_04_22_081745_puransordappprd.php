<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puransordappprd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('prd_id');
            $table->integer('prd_seq');
            $table->uuid('batch_id');
            $table->integer('batch_seq',);
            $table->uuid('pkg_id');
            $table->integer('pkg_seq',);
            $table->float('qty',20,4);
            
            $table->uuid('basepkg_id');
            $table->float('convpkg',20,4);
            $table->float('baseqty',20,4);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        
            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc');
            $table->foreign('prd_id')->references('id')->on('proste');
            $table->foreign('batch_id')->references('id')->on('prostebatch');
            $table->foreign('pkg_id')->references('id')->on('prosteunipackaging');
            $table->foreign('basepkg_id')->references('id')->on('prosteunipackaging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordappprd');
    }
};
