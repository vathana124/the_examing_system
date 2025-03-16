<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Use USING to cast the column to double precision
            DB::statement('ALTER TABLE questions ALTER COLUMN score TYPE DOUBLE PRECISION USING score::double precision');
        });
    }
    
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Revert the column type back to its original type (e.g., VARCHAR)
            DB::statement('ALTER TABLE questions ALTER COLUMN score TYPE VARCHAR USING score::VARCHAR');
        });
    }
};
