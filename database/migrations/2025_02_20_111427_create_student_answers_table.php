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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_exam_id')->constrained()->onDelete('cascade'); // Foreign key to student_exams table
            $table->foreignId('question_id')->constrained()->onDelete('cascade'); // Foreign key to questions table
            $table->string('selected_option')->nullable(); // Selected option (e.g., 'a', 'b', 'c', 'd')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
