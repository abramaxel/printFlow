<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessingDurationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Normal',    'slug' => 'normal',   'description' => 'Pengerjaan standar 3 hari kerja',          'days' => 3, 'multiplier' => 1.00],
            ['name' => 'Prioritas', 'slug' => 'priority', 'description' => 'Antrian didahulukan, 2 hari kerja',        'days' => 2, 'multiplier' => 1.50],
            ['name' => 'Express',   'slug' => 'express',  'description' => 'Cetak kilat, selesai hari ini / besok',    'days' => 1, 'multiplier' => 2.00],
        ];

        foreach ($rows as $r) {
            DB::table('processing_durations')->updateOrInsert(
                ['slug' => $r['slug']],
                $r + ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}