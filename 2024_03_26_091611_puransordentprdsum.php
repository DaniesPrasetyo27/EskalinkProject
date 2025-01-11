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
        Schema::create('puransordentprdsum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('prd_id');
            $table->string('prd_code',30);
            $table->string('prd_shortdesc',50);
            $table->string('prd_fulldesc',100);
            $table->integer('prd_seq');
            $table->uuid('sml_pkg_id');
            $table->string('sml_estpkg_code',30);
            $table->string('sml_estpkg_shortdesc',50);
            $table->string('sml_estpkg_fulldesc',100);
            $table->decimal('sml_estqty',20,4);
            $table->uuid('basepkg_id');
            $table->string('basepkg_code',30);
            $table->string('basepkg_shortdesc',50);
            $table->string('basepkg_fulldesc',100);
            $table->decimal('convpkg',20,4);
            $table->decimal('baseqty',20,4);
            $table->decimal('gross_amt',20,4);
            $table->decimal('disc_amt', 20,4);
            $table->decimal('dpp_amt', 20,4);
            $table->decimal('tax_amt', 20,4);
            $table->decimal('net_amt', 20,4);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

        
            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc');
            $table->foreign('prd_id')->references('id')->on('proste');
            $table->foreign('sml_pkg_id')->references('id')->on('prosteunipackaging');
            $table->foreign('basepkg_id')->references('id')->on('prosteunipackaging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentprdsum');
    }
};
