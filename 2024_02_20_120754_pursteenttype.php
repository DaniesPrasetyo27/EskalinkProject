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
        Schema::create('pursteenttype', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20);
            $table->string('description', 100);
            $table->string('type', 1); //not null, Note: "O: Order, R: Return"
            $table->boolean('allow_bonusprd')->default(false);
            $table->boolean('allow_gimmicks')->default(false);
            $table->boolean('allow_edit_disc')->default(false);
            $table->boolean('is_itface_ap')->default(true);
            $table->uuid('shipto_whtype_id');
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
        Schema::dropIfExists('pursteenttype');
    }
};
