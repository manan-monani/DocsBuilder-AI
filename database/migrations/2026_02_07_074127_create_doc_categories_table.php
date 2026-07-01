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
        Schema::create('doc_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_version_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->unique(['doc_version_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_categories');
    }
};
