<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QueueType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QueueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $queueTypes = [
            [
                'name' => 'Layanan Umum',
                'code' => 'A',
                'description' => 'Antrian untuk layanan umum',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Layanan Khusus',
                'code' => 'B',
                'description' => 'Antrian untuk layanan khusus',
                'color' => '#10b981',
            ],
            [
                'name' => 'Layanan VIP',
                'code' => 'V',
                'description' => 'Antrian untuk layanan VIP',
                'color' => '#f59e0b',
            ],
        ];

        foreach ($queueTypes as $type) {
            QueueType::create($type);
        }
    }
}
