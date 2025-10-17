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
        // 1. Create junction table for user_exams (replacing users.exam_ids JSON)
        Schema::create('user_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'exam_id']);
            $table->index('user_id');
            $table->index('exam_id');
        });

        // 2. Create junction table for user_teachers (replacing users.teachers JSON)
        Schema::create('user_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['student_id', 'teacher_id']);
            $table->index('student_id');
            $table->index('teacher_id');
            
            // Ensure student cannot be their own teacher
            // $table->check('student_id != teacher_id');
        });

        // 3. Create user_auth table for authentication fields
        Schema::create('user_auth', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            $table->string('token_2fa', 255)->nullable();
            $table->timestamp('token_2fa_expiry')->nullable();
            $table->string('email_otp', 255)->nullable();
            $table->boolean('verify_otp_false')->default(false);
            $table->string('otp_input_email', 255)->nullable();
            $table->timestamp('otp_resend_duration')->nullable();
            $table->timestamps();
        });

        // 4. Migrate existing data from users table
        DB::statement("
            INSERT INTO user_auth (user_id, token_2fa, token_2fa_expiry, email_otp, verify_otp_false, otp_input_email, otp_resend_duration, created_at, updated_at)
            SELECT id, token_2fa, token_2fa_expiry, email_otp, verify_otp_false, otp_input_email, otp_resend_duration, NOW(), NOW()
            FROM users
            WHERE token_2fa IS NOT NULL 
               OR email_otp IS NOT NULL 
               OR otp_input_email IS NOT NULL
        ");

        // 5. Fix score data type inconsistency in student_exams
        Schema::table('student_exams', function (Blueprint $table) {
            $table->double('score')->nullable()->change();
        });

        // 6. Add CHECK constraints for data validation
        DB::statement('ALTER TABLE exams ADD CONSTRAINT check_positive_duration CHECK (duration > 0)');
        DB::statement('ALTER TABLE exams ADD CONSTRAINT check_positive_exam_score CHECK (score >= 0 OR score IS NULL)');
        DB::statement('ALTER TABLE questions ADD CONSTRAINT check_positive_question_score CHECK (score >= 0 OR score IS NULL)');
        DB::statement('ALTER TABLE student_exams ADD CONSTRAINT check_valid_student_score CHECK (score >= 0 OR score IS NULL)');
        DB::statement('ALTER TABLE student_answers ADD CONSTRAINT check_positive_answer_score CHECK (score >= 0 OR score IS NULL)');

        // 7. Add performance indexes
        Schema::table('questions', function (Blueprint $table) {
            $table->index('exam_id', 'idx_questions_exam_id');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->index('student_exam_id', 'idx_student_answers_student_exam_id');
            $table->index('question_id', 'idx_student_answers_question_id');
        });

        Schema::table('student_exams', function (Blueprint $table) {
            $table->index('user_id', 'idx_student_exams_user_id');
            $table->index('exam_id', 'idx_student_exams_exam_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->index('user_id', 'idx_sessions_user_id');
            $table->index('last_activity', 'idx_sessions_last_activity');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->index('queue', 'idx_jobs_queue');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->index('question_id', 'idx_question_options_question_id');
        });

        // 8. Remove old JSON and auth columns from users table
        // WARNING: This will delete data! Make sure you've migrated it first!
        // Uncomment these lines after verifying data migration
        /*
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'exam_ids',
                'teachers',
                'token_2fa',
                'token_2fa_expiry',
                'email_otp',
                'verify_otp_false',
                'otp_input_email',
                'otp_resend_duration'
            ]);
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_exam_id');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropIndex('idx_student_answers_student_exam_id');
            $table->dropIndex('idx_student_answers_question_id');
        });

        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropIndex('idx_student_exams_user_id');
            $table->dropIndex('idx_student_exams_exam_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sessions_user_id');
            $table->dropIndex('idx_sessions_last_activity');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_queue');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropIndex('idx_question_options_question_id');
        });

        // Remove CHECK constraints
        DB::statement('ALTER TABLE exams DROP CONSTRAINT IF EXISTS check_positive_duration');
        DB::statement('ALTER TABLE exams DROP CONSTRAINT IF EXISTS check_positive_exam_score');
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS check_positive_question_score');
        DB::statement('ALTER TABLE student_exams DROP CONSTRAINT IF EXISTS check_valid_student_score');
        DB::statement('ALTER TABLE student_answers DROP CONSTRAINT IF EXISTS check_positive_answer_score');

        // Revert score type
        Schema::table('student_exams', function (Blueprint $table) {
            $table->integer('score')->nullable()->change();
        });

        // Restore columns to users if they were dropped
        /*
        Schema::table('users', function (Blueprint $table) {
            $table->json('exam_ids')->nullable();
            $table->json('teachers')->nullable();
            $table->string('token_2fa', 255)->nullable();
            $table->timestamp('token_2fa_expiry')->nullable();
            $table->string('email_otp', 255)->nullable();
            $table->boolean('verify_otp_false')->default(false);
            $table->string('otp_input_email', 255)->nullable();
            $table->timestamp('otp_resend_duration')->nullable();
        });
        */

        // Drop new tables
        Schema::dropIfExists('user_auth');
        Schema::dropIfExists('user_teachers');
        Schema::dropIfExists('user_exams');
    }
};