<?php

use App\Enums\TableStatus;
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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->char('name', 100);
            $table->text('description')->nullable();
            $table->char('status', 10)->default(TableStatus::NOT_READY);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('table_id')->nullable();
            $table->foreign('table_id')
                ->references('id')->on('tables')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign('reservations_table_id_foreign');
            $table->dropColumn('table_id');
        });
        Schema::dropIfExists('tables');
    }
};
