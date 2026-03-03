<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Add the column [cite: 2025-12-04]
        Schema::table('residents', function (Blueprint $table) {
            $table->string('census_cycle', 10)->nullable()->index()->after('id');
        });

        // 2. Backfill existing data [cite: 2025-12-04]
        $residents = DB::table('residents')->get();

        foreach ($residents as $resident) {
            $date = \Carbon\Carbon::parse($resident->created_at);
            $year = $date->year;
            $semester = ($date->month <= 6) ? 1 : 2;
            
            DB::table('residents')
                ->where('id', $resident->id)
                ->update(['census_cycle' => "{$year}-{$semester}"]);
        }
    }
};
