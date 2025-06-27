<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStudentCommand extends Command
{
    protected $signature = 'student:create {--email=} {--username=} {--first_name=} {--last_name=} {--student_id=} {--department=}';
    
    protected $description = 'Create a new student account';

    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Email');
        $username = $this->option('username') ?: $this->ask('Username');
        $firstName = $this->option('first_name') ?: $this->ask('First Name');
        $lastName = $this->option('last_name') ?: $this->ask('Last Name');
        $studentId = $this->option('student_id') ?: $this->ask('Student ID');
        
        $departments = Department::where('status', 'active')->pluck('name', 'id');
        $departmentId = $this->option('department') ?: $this->choice('Department', $departments->toArray());
        
        if (!is_numeric($departmentId)) {
            $departmentId = $departments->search($departmentId);
        }

        $password = Str::random(8);

        try {
            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'status' => 'active',
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'student_id' => $studentId,
                'department_id' => $departmentId,
                'level' => '100', // Default level
            ]);

            $this->info('Student created successfully!');
            $this->info("Email: {$email}");
            $this->info("Username: {$username}");
            $this->info("Temporary Password: {$password}");
            
        } catch (\Exception $e) {
            $this->error('Failed to create student: ' . $e->getMessage());
        }
    }
}
