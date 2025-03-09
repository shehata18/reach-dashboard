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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('client_name')->nullable();
            $table->foreignId('service_id')->constrained()->onDelete('restrict'); // Changed from category string
            $table->date('completion_date')->nullable();
            $table->json('technologies')->nullable(); // Changed to json
            $table->string('website_url')->nullable();
            $table->string('image')->nullable(); // Added project image
            $table->json('gallery')->nullable(); // Added for multiple project images
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0); // Added for ordering
            $table->boolean('is_active')->default(true); // Added for visibility control
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
