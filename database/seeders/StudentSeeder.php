
<?php

// database/seeders/StudentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        $levels = ['100', '200', '300', '400'];
        
        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'username' => 'student' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password'),
                'first_name' => 'Student',
                'last_name' => 'User ' . $i,
                'phone' => '080' . rand(10000000, 99999999),
                'status' => 'active',
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'student_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'department_id' => $departments->random()->id,
                'level' => $levels[array_rand($levels)],
                'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                'date_of_birth' => now()->subYears(rand(18, 25))->format('Y-m-d'),
            ]);
        }

        $this->command->info('50 students created successfully.');
    }
}