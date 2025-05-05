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
        Schema::create('user_property_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest users
            $table->unsignedBigInteger('property_id');
            $table->string('session_id')->nullable(); // For tracking guest users
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index(['user_id', 'property_id']);
            $table->index('session_id');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_property_views');
    }
};
