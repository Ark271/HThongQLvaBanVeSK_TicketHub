<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (Schema::hasColumn('events', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('events', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (!Schema::hasColumn('events', 'start_datetime')) {
                $table->dateTime('start_datetime')->after('title');
            }

            if (!Schema::hasColumn('events', 'end_datetime')) {
                $table->dateTime('end_datetime')->after('start_datetime');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (Schema::hasColumn('events', 'start_datetime')) {
                $table->dropColumn('start_datetime');
            }

            if (Schema::hasColumn('events', 'end_datetime')) {
                $table->dropColumn('end_datetime');
            }
        });
    }
};
