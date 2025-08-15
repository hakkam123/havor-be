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
        Schema::table('services', function (Blueprint $table) {

            $table->string('hero_image')->nullable()->after('description');
            $table->text('short_description')->nullable()->after('hero_image');
            

            $table->dropColumn(['price', 'duration', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->decimal('price', 10, 2)->default(0);
            $table->string('duration')->nullable();
            $table->boolean('is_featured')->default(false);
            
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['hero_image', 'short_description']);
        });
    }
};
