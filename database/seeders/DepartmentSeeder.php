<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Corporate',
            'Digital Marketing',
            'Creative',
            'Brand servicing',
            'Business Development',
            'IT',
            'Human Resources and Administration',
            'Finance',
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create(['name' => $department]);
        }
    }
}
