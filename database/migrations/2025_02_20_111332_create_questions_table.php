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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade'); // Foreign key to exams table
            $table->text('question_text')->nullable(); // The question text
            $table->string('option_a')->nullable(); // Option A
            $table->string('option_b')->nullable(); // Option B
            $table->string('option_c')->nullable(); // Option C
            $table->string('option_d')->nullable(); // Option D
            $table->string('correct_option')->nullable(); // Correct option (e.g., 'a', 'b', 'c', 'd')
            $table->string('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
