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
        Schema::create('sallaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('access_id');
            $table->foreign('access_id')
                ->references('id')
                ->on('accesses')
                ->onDelete('cascade');
            $table->unsignedBigInteger('nominal');
            $table->boolean('reducable')->default(false);
            $table->enum('given', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->string('rule', 255)->nullable()->default(null)->comment('if reducable, this rule related to Libbrary that stored in helpers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sallaries');
    }
};
