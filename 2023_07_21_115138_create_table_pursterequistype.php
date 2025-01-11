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
        Schema::create('pursterequistype', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20);
            $table->string('description', 100);
            $table->string('type', 1);
            $table->integer('appr_due_day');
            $table->boolean('split_top_prd')->default(false);
            $table->boolean('split_timely_paym')->default(false);
            $table->integer('std_uom')->default(1);
            $table->boolean('allow_chg_uom')->default(false);
            $table->boolean('auto_gen_prd')->default(true);
            $table->boolean('allow_chg_prd')->default(false);
            $table->boolean('allow_add_qty')->default(false);
            $table->boolean('allow_red_qty')->default(true);
            $table->boolean('mand_sono_ref')->default(false);
            $table->boolean('mand_prno_ref')->default(false);
            $table->boolean('mand_reason')->default(false);
            $table->boolean('is_itface_po')->default(true);
            $table->boolean('def_pobr_eq_prbr')->default(true);
            $table->boolean('def_podt_eq_prdt')->default(true);
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
        Schema::dropIfExists('pursterequistype');
    }
};
