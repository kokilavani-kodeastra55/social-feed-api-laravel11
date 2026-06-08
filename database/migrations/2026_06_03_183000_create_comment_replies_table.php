<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create comment_replies table
        Schema::create('comment_replies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('comment_id');
            $table->text('comment');
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('comment_id')->references('id')->on('comments')->cascadeOnDelete();
            $table->index(['comment_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // 2. Add replies_count column to comments table
        Schema::table('comments', function (Blueprint $table): void {
            $table->unsignedInteger('replies_count')->default(0)->after('comment');
        });

        // 3. Delete reply records from comments table
        DB::table('comments')->whereNotNull('parent_id')->delete();

        // 4. Drop parent_id column and foreign key from comments table
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add parent_id column back to comments table
        Schema::table('comments', function (Blueprint $table): void {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('comments')->cascadeOnDelete();
        });

        // Drop replies_count column from comments table
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropColumn('replies_count');
        });

        Schema::dropIfExists('comment_replies');
    }
};
