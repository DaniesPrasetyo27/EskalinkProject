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
        Schema::create('puransordentsts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_doc_id');
            $table->string('doc_sts', 2);
            $table->string('sts_shortdesc', 50);
            $table->boolean('is_active')->default(true);
            $table->string('doc_remark', 100);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 30);
            $table->softDeletes();
            $table->string('deleted_by', 30)->nullable();

            $table->foreign('doc_sts')->references('code')->on('purstereqsts')->onDelete('cascade');
            $table->foreign('parent_doc_id')->references('id')->on('puransordentdoc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puransordentsts');
    }
};
