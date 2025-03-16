<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Add this line to use DB::statement

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to alter the column with explicit casting
        DB::statement('ALTER TABLE student_answers ALTER COLUMN selected_option TYPE BIGINT USING selected_option::BIGINT');

        // Add a foreign key constraint to link selected_option with options.id
        Schema::table('student_answers', function (Blueprint $table) {
            $table->foreign('selected_option')
                  ->references('id')
                  ->on('question_options')
                  ->onDelete('cascade'); // Optional: Define the behavior on delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['selected_option']);
        });

        // Revert the column back to VARCHAR
        DB::statement('ALTER TABLE student_answers ALTER COLUMN selected_option TYPE VARCHAR USING selected_option::VARCHAR');
    }
};