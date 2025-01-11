<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puransordentdiscprd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('prd_id');
            $table->integer('prd_seq');
            $table->uuid('level_id')->nullable();
            $table->integer('level_seq')->nullable();
            $table->string('level_name', 50)->nullable();
            $table->string('base_disc', 1)->nullable();
            $table->float('bef_disc_amt', 20.4)->default('0');
            $table->uuid('disc_id')->nullable();
            $table->float('disc_rate_pct',20.4)->default('0');
            $table->float('disc_rate_val',20.4)->default('0');
            $table->float('disc_rate_amt',20.4)->default('0');
            $table->float('disc_in_val',20.4)->default('0');
            $table->float('tot_disc_amt',20.4)->default('0');
            $table->float('aft_disc_amt',20.4)->default('0');
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc')->onDelete('cascade');
            $table->foreign('prd_id')->references('id')->on('proste')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('purstedislevel')->onDelete('cascade');
            $table->foreign('disc_id')->references('id')->on('purstedispriprd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentdiscprd');
    }
};
