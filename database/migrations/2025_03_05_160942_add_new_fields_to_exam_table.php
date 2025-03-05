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
        Schema::table('exams', function (Blueprint $table) {
            // Add 'created_by' and 'updated_by' columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Add foreign key constraints for 'created_by' and 'updated_by'
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // Set to NULL when the referenced user is deleted

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // Set to NULL when the referenced user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop the columns
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
