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
        Schema::dropIfExists('experiences');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('job_title');
            $table->enum('job_type', ['full time', 'part time', 'contract']);
            $table->string('company_name');
            $table->string('job_location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('job_description')->nullable();
            $table->timestamps();
        });
    }
};
