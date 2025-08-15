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
            $table->text('description');
            $table->text('content');
            $table->string('image_url');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('client_name'); 
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->date('project_date');
            $table->enum('status', ['planning', 'in_progress', 'completed'])->default('planning');
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
