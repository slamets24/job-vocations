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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 100);
            $table->text('description');
            $table->string('location', 100);
            $table->decimal('salary', 15, 0)->nullable();
            $table->enum('education', ['smp', 'slta/sma/smk', 'd3', 'd4', 'sarjana s1', 'sarjana s2']);
            $table->string('slug');
            $table->enum('job_type', ['full time', 'part time', 'contract']);
            $table->date('closing_date')->nullable();
            $table->unsignedInteger('province_id');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('district_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
