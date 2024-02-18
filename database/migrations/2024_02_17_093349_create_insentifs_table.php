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
        Schema::create('insentifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('access_id');
            $table->foreign('access_id')
                ->references('id')
                ->on('accesses')
                ->onDelete('cascade');
            $table->unsignedInteger('sales_qty')->comment('In Period');
            $table->string('insentive', 255);
            $table->enum('type', ['money', 'thing'])->default('money');
            $table->enum('period', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insentifs');
    }
};
