<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // Thời gian bắt đầu
            if (!Schema::hasColumn('events', 'start_time')) {
                $table->dateTime('start_time')->nullable()->after('title');
            }

            // Thời gian kết thúc
            if (!Schema::hasColumn('events', 'end_time')) {
                $table->dateTime('end_time')->nullable()->after('start_time');
            }

            // Địa điểm
            if (!Schema::hasColumn('events', 'location')) {
                $table->string('location')->nullable()->after('end_time');
            }

            // Số người tham gia tối đa
            if (!Schema::hasColumn('events', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (Schema::hasColumn('events', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('events', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (Schema::hasColumn('events', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('events', 'max_participants')) {
                $table->dropColumn('max_participants');
            }
        });
    }
};
