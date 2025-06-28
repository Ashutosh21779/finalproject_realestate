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
        // Add indexes for wishlist table
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('user_id', 'idx_wishlists_user_id');
            $table->index('property_id', 'idx_wishlists_property_id');
            $table->index(['user_id', 'property_id'], 'idx_wishlists_user_property');
        });

        // Add indexes for properties table for recommendation queries
        Schema::table('properties', function (Blueprint $table) {
            $table->index('ptype_id', 'idx_properties_ptype_id');
            $table->index('state', 'idx_properties_state');
            $table->index('status', 'idx_properties_status');
            $table->index(['status', 'ptype_id'], 'idx_properties_status_ptype');
            $table->index(['status', 'state'], 'idx_properties_status_state');
            $table->index('bedrooms', 'idx_properties_bedrooms');
            $table->index('bathrooms', 'idx_properties_bathrooms');
            $table->index('city', 'idx_properties_city');
        });

        // Add indexes for user_property_views if the table exists
        if (Schema::hasTable('user_property_views')) {
            Schema::table('user_property_views', function (Blueprint $table) {
                if (!Schema::hasIndex('user_property_views', 'idx_user_property_views_user_id')) {
                    $table->index('user_id', 'idx_user_property_views_user_id');
                }
                if (!Schema::hasIndex('user_property_views', 'idx_user_property_views_property_id')) {
                    $table->index('property_id', 'idx_user_property_views_property_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop wishlist indexes
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlists_user_id');
            $table->dropIndex('idx_wishlists_property_id');
            $table->dropIndex('idx_wishlists_user_property');
        });

        // Drop properties indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('idx_properties_ptype_id');
            $table->dropIndex('idx_properties_state');
            $table->dropIndex('idx_properties_status');
            $table->dropIndex('idx_properties_status_ptype');
            $table->dropIndex('idx_properties_status_state');
            $table->dropIndex('idx_properties_bedrooms');
            $table->dropIndex('idx_properties_bathrooms');
            $table->dropIndex('idx_properties_city');
        });

        // Drop user_property_views indexes if table exists
        if (Schema::hasTable('user_property_views')) {
            Schema::table('user_property_views', function (Blueprint $table) {
                if (Schema::hasIndex('user_property_views', 'idx_user_property_views_user_id')) {
                    $table->dropIndex('idx_user_property_views_user_id');
                }
                if (Schema::hasIndex('user_property_views', 'idx_user_property_views_property_id')) {
                    $table->dropIndex('idx_user_property_views_property_id');
                }
            });
        }
    }
};
