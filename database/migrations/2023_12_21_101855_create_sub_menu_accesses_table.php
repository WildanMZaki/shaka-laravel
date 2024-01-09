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
        Schema::create('sub_menu_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_menu_id');
            $table->foreign('sub_menu_id')
                ->references('id')
                ->on('sub_menus')
                ->onDelete('cascade');
            $table->unsignedBigInteger('access_id');
            $table->foreign('access_id')
                ->references('id')
                ->on('accesses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_menu_accesses');
    }
};
