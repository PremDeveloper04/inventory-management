<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worker; // ✅ ADD THIS
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // \App\Models\Material::insert([
        //     ['name' => 'Clay', 'price' => 5],
        //     ['name' => 'Sand', 'price' => 3],
        //     ['name' => 'Water', 'price' => 0],
        // ]);

        // $total = 10000000;
        // $chunkSize = 1000;

        // for ($i = 0; $i < $total; $i += $chunkSize) {
        //     $workers = Worker::factory()->count($chunkSize)->make()->toArray();
        //     Worker::insert($workers);
        // }
    }
}