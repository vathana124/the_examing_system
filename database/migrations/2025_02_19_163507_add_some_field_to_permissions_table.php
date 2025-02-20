<?php

use App\Models\User;
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
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('type', [User::TYPE_ADMIN, User::TYPE_STUDENT])->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('sort')->default(1);

            $table->foreign('parent_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['parent_id']);
            $table->dropColumn('type');
            $table->dropColumn('description');
            $table->dropColumn('sort');
        });
    }
};
