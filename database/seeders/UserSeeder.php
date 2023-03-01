<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(
            [
                'nickname' => 'Test Admin User',
                'email' => 'admin@test.com',
                'password' => '$2y$10$/4OC8ZMqeCTMzb9RhRca1evVw2YDPP1wuIGWP58NqADCyp.0G.U.W',
            ],
        )->assignRole(['Admin']);

        User::create(
            [
                'nickname' => 'Test Player User',
                'email' => 'player@test.com',
                'password' => '$2y$10$/4OC8ZMqeCTMzb9RhRca1evVw2YDPP1wuIGWP58NqADCyp.0G.U.W',
            ]
        )->assignRole(['Player']);
    }
}
