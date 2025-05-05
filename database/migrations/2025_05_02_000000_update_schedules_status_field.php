<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the column type to string if it's not already
        Schema::table('schedules', function (Blueprint $table) {
            // Check if the status column exists and is not a string
            if (Schema::hasColumn('schedules', 'status')) {
                $table->string('status')->default('pending')->change();
            }
        });

        // Update existing records with '0' status to 'pending'
        DB::table('schedules')
            ->where('status', '0')
            ->orWhere('status', 0)
            ->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('status')->default('0')->change();
        });
    }
};
