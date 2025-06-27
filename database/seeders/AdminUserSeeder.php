<?php
// database/seeders/AdminUserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => 'admin123',
            //'first_name' => 'System',
            //'last_name' => 'Administrator',
            'role_id' => $adminRole->id,
            //'status' => 'active',
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $admin->id,
        ]);
    }
}