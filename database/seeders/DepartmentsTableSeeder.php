<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed dummy departments
        $departments = [
            ['name' => 'Cardiology'],
            ['name' => 'Orthopedics'],
            ['name' => 'Neurology'],
            ['name' => 'Gynecology'],
            ['name' => 'Pediatrics'],
        ];

        Department::insert($departments);
    }
}
