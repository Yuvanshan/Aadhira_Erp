<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PERFORMANCE OPTIMIZATION: Add missing indexes for faster N+1 query lookups
        
        // Index for parent_sell_line_id + children_type lookups
        // Used when fetching modifiers and combo items from transaction_sell_lines
        if (Schema::hasTable('transaction_sell_lines')) {
            Schema::table('transaction_sell_lines', function (Blueprint $table) {
                // Check if index doesn't already exist before creating
                if (!$this->indexExists('transaction_sell_lines', 'idx_parent_children')) {
                    $table->index(['parent_sell_line_id', 'children_type'], 'idx_parent_children');
                }
            });
        }

        // Index for transaction_id + variation_id lookups
        // Used when processing transaction line items
        if (Schema::hasTable('transaction_sell_lines')) {
            Schema::table('transaction_sell_lines', function (Blueprint $table) {
                if (!$this->indexExists('transaction_sell_lines', 'idx_transaction_variation')) {
                    $table->index(['transaction_id', 'variation_id'], 'idx_transaction_variation');
                }
            });
        }

        // Index for product_id + id lookups on variations
        // Used when eager loading variations
        if (Schema::hasTable('variations')) {
            Schema::table('variations', function (Blueprint $table) {
                if (!$this->indexExists('variations', 'idx_product_variation')) {
                    $table->index(['product_id', 'id'], 'idx_product_variation');
                }
            });
        }

        // Index for variation_id + location_id lookups in inventory details
        // Used when fetching lot numbers
        if (Schema::hasTable('purchase_lines_inventory_details')) {
            Schema::table('purchase_lines_inventory_details', function (Blueprint $table) {
                if (!$this->indexExists('purchase_lines_inventory_details', 'idx_variation_location')) {
                    $table->index(['variation_id', 'location_id'], 'idx_variation_location');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the added indexes
        if (Schema::hasTable('transaction_sell_lines')) {
            Schema::table('transaction_sell_lines', function (Blueprint $table) {
                $table->dropIndex('idx_parent_children');
            });
        }

        if (Schema::hasTable('transaction_sell_lines')) {
            Schema::table('transaction_sell_lines', function (Blueprint $table) {
                $table->dropIndex('idx_transaction_variation');
            });
        }

        if (Schema::hasTable('variations')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->dropIndex('idx_product_variation');
            });
        }

        if (Schema::hasTable('purchase_lines_inventory_details')) {
            Schema::table('purchase_lines_inventory_details', function (Blueprint $table) {
                $table->dropIndex('idx_variation_location');
            });
        }
    }

    /**
     * Helper method to check if index exists (without Doctrine DBAL)
     */
    private function indexExists($table, $index)
    {
        $results = \DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$index]
        );
        return ! empty($results);
    }
}
