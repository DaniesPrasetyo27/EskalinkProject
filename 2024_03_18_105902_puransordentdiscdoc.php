<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puransordentdiscdoc', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('prd_id');
            $table->integer('prd_seq');
            $table->decimal('disc_rate_pct', 20,4);
            $table->decimal('disc_rate_val', 20,4);
            $table->decimal('disc_rate_amt', 20,4);
            $table->decimal('disc_in_val', 20,4);
            $table->decimal('tot_disc_amt', 20,4);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc')->onDelete('cascade');
            $table->foreign('prd_id')->references('id')->on('proste')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentdiscdoc');
    }
};
