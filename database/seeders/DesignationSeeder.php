<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            'Brand Servicing' => [
                'Head of Brand Management',
                'Assistant Brand Manager',
                'Senior Brand Executive',
                'Brand Executive',
                'Trainee',
            ],
            'Business Development' => [
                'Senior Manager - Business Development',
                'Manager - Business Development',
                'Senior Executive - Business Development',
                'Business Development Executive',
                'Junior Business Development Executive',
                'Trainee',
            ],
            'Creative' => [
                'Executive Creative Director',
                'Associate Creative Director',
                'Senior Creative Copywriter - English',
                'Senior Creative Copywriter - Sinhala',
                'Creative Copywriter - English',
                'Creative Copywriter - Sinhala',
                'Senior Art Director',
                'Art Director',
                'Graphic Artist',
            ],
            'Digital Marketing' => [
                'Head of Digital Marketing',
                'Senior Manager - Digital Marketing',
                'Manager - Digital Marketing',
                'Assistant Manager - Digital Marketing',
                'Senior Executive - Digital Marketing',
                'Executive - Digital Marketing',
                'Creative Copywriter - Digital (English)',
                'Creative Copywriter - Digital (Sinhala)',
                'Creative Copywriter - Digital (English/Tamil)',
                'Senior Creative Designer',
                'Junior Creative Designer',
                'Creative Designer',
                'Project Coordinator',
                'Art Director',
                'AI Content Creation & Design Specialist',
                'Manager - Performance Marketing',
                'Video Producer & Editor (Socials)',
                'Trainee',
            ],
            'Finance' => [
                'Senior Manager - Finance',
                'Assitant Manager - Finance',
                'Junior Finance Executive',
            ],
            'Human Resources & Administration' => [
                'Senior Executive - Human Resource & Operations',
                'Admin Assistant',
            ],
            'IT' => [
                'Head of IT',
                'Junior Web Developer',
                'IT Administrator',
                'Business Development Executive - IT',
                'AI Developer',
                'Trainee',
            ],
        ];

        foreach ($designations as $departmentName => $roleNames) {
            $department = \App\Models\Department::firstOrCreate(['name' => $departmentName]);
            
            foreach ($roleNames as $roleName) {
                \App\Models\Designation::firstOrCreate([
                    'name' => $roleName,
                    'department_id' => $department->id,
                ]);
            }
        }
    }
}
