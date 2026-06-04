<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_requests', fn (Blueprint $t) => $t->dropColumn('hours'));
    }

    public function down(): void
    {
        Schema::table('booking_requests', fn (Blueprint $t) => $t->integer('hours')->nullable()->after('date'));
    }
};
