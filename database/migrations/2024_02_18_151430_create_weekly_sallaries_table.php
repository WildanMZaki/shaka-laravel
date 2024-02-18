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
        Schema::create('weekly_sallaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->boolean('presence_status')->default(true);
            $table->integer('total_sold')->default(0)->comment('in period');
            $table->integer('min_sold')->default(0)->comment('total minimum, khusus untuk sales freelancer');
            $table->integer('uang_absen')->default(0);
            $table->integer('insentive')->default(0);
            $table->integer('main_sallary');
            $table->integer('kasbon')->default(0);
            $table->integer('unpaid_keep')->default(0);
            $table->integer('total_kasbon')->default(0)->comment('akumulasi 2 kolom sebelumnya');
            $table->integer('total');
            $table->enum('status', ['ungiven', 'given'])->default('ungiven');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_sallaries');
    }
};
