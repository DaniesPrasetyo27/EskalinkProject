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
        Schema::create('purstereqfrmurmus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pr_parm_id');
            $table->text('parm_rumus')->nullable();

            $table->foreign('pr_parm_id')->references('id')->on('purstereqfrmu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purstereqfrmurmus');
    }
};
