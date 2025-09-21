<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QueueType;
use App\Models\Setting;
use App\Models\Queue;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
    {
        $this->call([
            UserSeeder::class,
            QueueTypeSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
