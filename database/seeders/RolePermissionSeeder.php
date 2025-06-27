<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full system access'
        ]);

        $lecturerRole = Role::create([
            'name' => 'lecturer',
            'display_name' => 'Lecturer',
            'description' => 'Can create and manage exams and questions'
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'display_name' => 'Student',
            'description' => 'Can take exams and view results'
        ]);

        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'User Management'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'User Management'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'User Management'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'User Management'],
            
            // Department Management
            ['name' => 'departments.view', 'display_name' => 'View Departments', 'group' => 'Department Management'],
            ['name' => 'departments.create', 'display_name' => 'Create Departments', 'group' => 'Department Management'],
            ['name' => 'departments.edit', 'display_name' => 'Edit Departments', 'group' => 'Department Management'],
            ['name' => 'departments.delete', 'display_name' => 'Delete Departments', 'group' => 'Department Management'],
            
            // Subject Management
            ['name' => 'subjects.view', 'display_name' => 'View Subjects', 'group' => 'Subject Management'],
            ['name' => 'subjects.create', 'display_name' => 'Create Subjects', 'group' => 'Subject Management'],
            ['name' => 'subjects.edit', 'display_name' => 'Edit Subjects', 'group' => 'Subject Management'],
            ['name' => 'subjects.delete', 'display_name' => 'Delete Subjects', 'group' => 'Subject Management'],
            
            // Question Management
            ['name' => 'questions.view', 'display_name' => 'View Questions', 'group' => 'Question Management'],
            ['name' => 'questions.create', 'display_name' => 'Create Questions', 'group' => 'Question Management'],
            ['name' => 'questions.edit', 'display_name' => 'Edit Questions', 'group' => 'Question Management'],
            ['name' => 'questions.delete', 'display_name' => 'Delete Questions', 'group' => 'Question Management'],
            ['name' => 'questions.import', 'display_name' => 'Import Questions', 'group' => 'Question Management'],
            
            // Exam Management
            ['name' => 'exams.view', 'display_name' => 'View Exams', 'group' => 'Exam Management'],
            ['name' => 'exams.create', 'display_name' => 'Create Exams', 'group' => 'Exam Management'],
            ['name' => 'exams.edit', 'display_name' => 'Edit Exams', 'group' => 'Exam Management'],
            ['name' => 'exams.delete', 'display_name' => 'Delete Exams', 'group' => 'Exam Management'],
            ['name' => 'exams.publish', 'display_name' => 'Publish Exams', 'group' => 'Exam Management'],
            
            // Exam Taking
            ['name' => 'exams.take', 'display_name' => 'Take Exams', 'group' => 'Exam Taking'],
            ['name' => 'exams.review', 'display_name' => 'Review Exam Results', 'group' => 'Exam Taking'],
            
            // Reporting
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reporting'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'group' => 'Reporting'],
            
            // System Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'group' => 'System Settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'group' => 'System Settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assign permissions to roles
        $allPermissions = Permission::all();
        
        // Admin gets all permissions
        $adminRole->permissions()->attach($allPermissions);
        
        // Lecturer permissions
        $lecturerPermissions = Permission::whereIn('name', [
            'subjects.view',
            'questions.view', 'questions.create', 'questions.edit', 'questions.delete', 'questions.import',
            'exams.view', 'exams.create', 'exams.edit', 'exams.delete', 'exams.publish',
            'reports.view', 'reports.export'
        ])->get();
        $lecturerRole->permissions()->attach($lecturerPermissions);
        
        // Student permissions
        $studentPermissions = Permission::whereIn('name', [
            'exams.take', 'exams.review'
        ])->get();
        $studentRole->permissions()->attach($studentPermissions);
    }
}
