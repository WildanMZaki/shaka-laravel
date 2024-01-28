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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('access_id')->default(3);
            $table->foreign('access_id')
                ->references('id')
                ->on('accesses')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('photo')->nullable();
            $table->string('email');
            $table->string('phone', 16);
            $table->string('password');
            $table->boolean('is_employee')->default(true);
            $table->boolean('active')->default(true)->comment('1=active, 0=inactive');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
