<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Worker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 Important for large data
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | MATERIAL SEEDING
        |--------------------------------------------------------------------------
        */
        $materials = [];

        for ($i = 1; $i <= 50; $i++) {
            $materials[] = [
                'name' => 'Material ' . $i,
                'price' => rand(1, 100),
            ];
        }

        DB::table('materials')->insert($materials);

        /*
        |--------------------------------------------------------------------------
        | WORKER SEEDING
        |--------------------------------------------------------------------------
        */

        $total = 10000000; // ⚠️ Start with 100k (increase later)
        $chunkSize = 1000;

        for ($i = 0; $i < $total; $i += $chunkSize) {

            $workers = Worker::factory()
                ->count($chunkSize)
                ->make()
                ->map(function ($worker) {

                    // 🔥 Random realistic date
                    $date = Carbon::now()->subDays(rand(0, 365 * 3));

                    return [
                        'name' => $worker->name,
                        'email' => $worker->email,
                        'phone' => $worker->phone,

                        'state' => $worker->state,
                        'city' => $worker->city,
                        'country' => $worker->country, // already "India"

                        'status' => $worker->status,
                        'experience' => $worker->experience,
                        'salary' => $worker->salary,

                        'joined_at' => $date,

                        // ✅ IMPORTANT
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                })
                ->toArray();

            DB::table('workers')->insert($workers);

            // Optional progress log
            echo "Inserted: " . ($i + $chunkSize) . "\n";
        }
    }
}