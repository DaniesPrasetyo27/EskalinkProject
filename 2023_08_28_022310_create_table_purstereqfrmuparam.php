<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purstereqfrmuparam', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pr_frml_id');
            $table->string('parm_als', 50);
            $table->text('parm_src');

            $table->foreign('pr_frml_id')->references('id')->on('purstereqfrmu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purstereqfrmuparam');
    }
};
