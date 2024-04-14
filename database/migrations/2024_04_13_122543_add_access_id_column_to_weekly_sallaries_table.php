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
        Schema::table('weekly_sallaries', function (Blueprint $table) {
            $table->unsignedBigInteger('access_id')->nullable()->default(null)->comment('when row created')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_sallaries', function (Blueprint $table) {
            $table->dropColumn('access_id');
        });
    }
};
