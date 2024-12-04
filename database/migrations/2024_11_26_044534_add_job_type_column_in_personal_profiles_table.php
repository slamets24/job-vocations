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
        Schema::table('personal_profiles', function (Blueprint $table) {
            $table->enum('job_type', ['full time', 'part time', 'contract']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_profiles', function (Blueprint $table) {
            $table->dropColumn('job_type');
        });
    }
};
