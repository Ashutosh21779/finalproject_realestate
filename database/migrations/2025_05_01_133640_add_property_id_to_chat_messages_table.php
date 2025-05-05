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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('receiver_id');
            $table->boolean('read_status')->default(0)->after('msg'); // 0 = unread, 1 = read

            // Optional: Add foreign key constraint if you have a properties table
            // $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Optional: Drop foreign key first if you added it
            // $table->dropForeign(['property_id']); 
            $table->dropColumn('property_id');
            $table->dropColumn('read_status');
        });
    }
};
