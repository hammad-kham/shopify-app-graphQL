<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rules')->insert([
            [
                'title' => 'Rule One',
                'thumbnail' => 'https://via.placeholder.com/200', 
                'priority' => 1,
                'status' => true,
            ],
            [
                'title' => 'Rule Two',
                'thumbnail' => 'https://via.placeholder.com/200',
                'priority' => 2,
                'status' => true,
            ],
            [
                'title' => 'Rule Three',
                'thumbnail' => 'https://via.placeholder.com/200',
                'priority' => 3,
                'status' => false,
            ],
        ]);
    }
}
