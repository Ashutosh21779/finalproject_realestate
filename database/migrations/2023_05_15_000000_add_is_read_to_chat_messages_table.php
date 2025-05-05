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
            // Add is_read column if it doesn't exist
            if (!Schema::hasColumn('chat_messages', 'is_read')) {
                $table->boolean('is_read')->default(0)->after('msg'); // 0 = unread, 1 = read
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
