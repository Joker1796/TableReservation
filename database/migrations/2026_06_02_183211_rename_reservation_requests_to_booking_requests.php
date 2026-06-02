<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('reservation_requests', 'booking_requests');
        Schema::rename('reservation_request_user', 'booking_request_user');

        Schema::table('booking_request_user', function (Blueprint $table) {
            $table->renameColumn('reservation_request_id', 'booking_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_request_user', function (Blueprint $table) {
            $table->renameColumn('booking_request_id', 'reservation_request_id');
        });

        Schema::rename('booking_request_user', 'reservation_request_user');
        Schema::rename('booking_requests', 'reservation_requests');
    }
};
