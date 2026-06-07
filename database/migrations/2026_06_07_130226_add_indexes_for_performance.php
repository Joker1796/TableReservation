<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_user', function (Blueprint $table): void {
            $table->index('seen_by_admin');
        });

        Schema::table('invites', function (Blueprint $table): void {
            $table->index(['target_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('event_user', function (Blueprint $table): void {
            $table->dropIndex(['seen_by_admin']);
        });

        Schema::table('invites', function (Blueprint $table): void {
            $table->dropIndex(['target_id', 'status']);
        });
    }
};
