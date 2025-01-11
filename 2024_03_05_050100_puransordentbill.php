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
        Schema::create('puransordentbill', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->uuid('top_id');
            $table->string('top_code',30);
            $table->string('top_shortdesc',50);
            $table->string('top_fulldesc',100)->nullable();
            $table->integer('top_top_days');
            $table->uuid('paytp_id');
            $table->string('paytp_code',30);
            $table->string('paytp_shortdesc', 50);
            $table->string('paytp_fulldesc', 100)->nullable();
            $table->uuid('curr_id');
            $table->string('curr_code',30);
            $table->string('curr_shortdesc', 50);
            $table->string('curr_fulldesc', 100)->nullable();
            $table->uuid('tax_id');
            $table->string('tax_code',30);
            $table->string('tax_shortdesc', 50);
            $table->string('tax_fulldesc', 100);
            $table->uuid('tax_rate_id');
            $table->float('tax_rate_val', 20.4);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc')->onDelete('cascade');
            $table->foreign('top_id')->references('id')->on('tpystetop')->onDelete('cascade');
            $table->foreign('paytp_id')->references('id')->on('tpystetype')->onDelete('cascade');
            $table->foreign('curr_id')->references('id')->on('curste')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxsteh')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('id')->on('taxsted'); //Tidak Bisa pakai cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentbill');
    }
};
