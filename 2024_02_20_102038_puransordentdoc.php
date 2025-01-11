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
        Schema::create('puransordentdoc', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ent_type_id');
            $table->string('ent_type_desc',100);
            $table->uuid('princ_id');
            $table->string('princ_code',30)->nullable();
            $table->string('princ_shortname',50)->nullable();
            $table->string('princ_fullname',100)->nullable();
            $table->string('doc_no',30);
            $table->date('doc_date',);
            $table->string('doc_ref',100)->nullable();
            $table->string('doc_remark',100)->nullable();
            $table->uuid('rqs_doc_id')->nullable();
            $table->string('rqs_doc_no',30)->nullable();
            $table->date('req_delv_date')->nullable();
            $table->date('prm_ship_date')->nullable();
            $table->date('prm_delv_date',15)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

            $table->foreign('ent_type_id')->references('id')->on('pursteenttype');
            $table->foreign('princ_id')->references('id')->on('pristeprofile');
            // $table->foreign('rqs_doc_id')->references('id')->on('puransreqentdoc');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentdoc');
    }
};
