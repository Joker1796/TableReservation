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
        Schema::create('reservation_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->foreign('table_id')
                ->references('id')->on('tables')
                ->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->timestamp('date');
            $table->tinyInteger('hours')->nullable();
            $table->tinyInteger('status')->default(0);
        });

        Schema::create('reservation_request_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_request_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('reservation_request_id')->references('id')->on('reservation_requests');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_request_user');
        Schema::dropIfExists('reservation_requests');
    }
};
