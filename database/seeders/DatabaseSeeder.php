<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Schema::disableForeignKeyConstraints();

        // Truncate the user and task tables
        User::truncate();
        Task::truncate();

        // Enable foreign key checks
        \Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
        ]);

    }
}
