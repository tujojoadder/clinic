<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->unsignedTinyInteger('weekday')->unique(); // 0=শনিবার ... 6=শুক্রবার
        });

        Schema::create('doctor_day', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('day_id');
            $table->primary(['doctor_id', 'day_id']);
            $table->index('day_id');
        });

        // নাম, weekday নম্বর, পুরনো লেখা মেলানোর অংশ
        $days = [
            ['শনিবার', 0, 'শনি'],
            ['রবিবার', 1, 'রবি'],
            ['সোমবার', 2, 'সোম'],
            ['মঙ্গলবার', 3, 'মঙ্গল'],
            ['বুধবার', 4, 'বুধ'],
            ['বৃহস্পতিবার', 5, 'বৃহস্পতি'],
            ['শুক্রবার', 6, 'শুক্র'],
        ];

        $dayIds = [];
        foreach ($days as [$name, $weekday, $stem]) {
            $dayIds[$stem] = DB::table('days')->insertGetId(['name' => $name, 'weekday' => $weekday]);
        }

        // পুরনো available_days লেখা থেকে যতটা সম্ভব দিন বসিয়ে দেওয়া
        foreach (DB::table('doctors')->whereNotNull('available_days')->get(['id', 'available_days']) as $doctor) {
            foreach ($dayIds as $stem => $dayId) {
                if (str_contains($doctor->available_days, $stem)) {
                    DB::table('doctor_day')->insertOrIgnore(['doctor_id' => $doctor->id, 'day_id' => $dayId]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_day');
        Schema::dropIfExists('days');
    }
};
