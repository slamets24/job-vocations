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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('image_profile')->nullable();
            $table->string('company_name', 100);
            $table->string('company_address', 200);
            $table->text('description')->nullable();
            $table->string('website', 100)->nullable();
            $table->string('slug');
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
        Schema::dropIfExists('company_profiles');
    }
};
