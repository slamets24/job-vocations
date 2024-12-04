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
        Schema::table('job_postings', function (Blueprint $table) {
            $table->enum('education', ['sd', 'smp', 'slta/sma/smk', 'd3', 'd4', 's1', 's2', 's3'])
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->enum('education', ['smp', 'slta/sma/smk', 'd3', 'd4', 'sarjana s1', 'sarjana s2'])
                ->change();
        });
    }
};
