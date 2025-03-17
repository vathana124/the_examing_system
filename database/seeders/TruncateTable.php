<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('question_options')->truncate();
        // DB::table('questions')->truncate();
        // DB::table('exams')->truncate();
        DB::table('student_answers')->truncate();
        DB::table('student_exams')->truncate();
    }
}
