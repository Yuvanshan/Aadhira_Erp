<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('activity_log', 'location_id')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->integer('location_id')->after('business_id')->nullable()->index();
            });
        }

        // Historically populate location_id for transaction-related activities
        try {
            DB::statement("
                UPDATE activity_log al
                INNER JOIN transactions t ON al.subject_id = t.id
                SET al.location_id = t.location_id
                WHERE al.subject_type LIKE '%Transaction' AND al.location_id IS NULL
            ");
        } catch (\Exception $e) {
            // Log warning or handle quietly if transaction table is empty or doesn't match
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('activity_log', 'location_id')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropColumn('location_id');
            });
        }
    }
};
