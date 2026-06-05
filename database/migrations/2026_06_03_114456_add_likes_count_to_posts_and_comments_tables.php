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
        Schema::table('posts', function (Blueprint $table): void {
            $table->unsignedInteger('likes_count')->default(0)->after('description');
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->unsignedInteger('likes_count')->default(0)->after('comment');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn('likes_count');
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->dropColumn('likes_count');
        });
    }
};
