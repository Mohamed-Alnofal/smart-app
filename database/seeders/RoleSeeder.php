<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('roles')->insert([
           ['name' => 'admin'],
           ['name' => 'manager'],
           ['name' => 'student'],
]);
 // الحصول على الـ role_id لكل دور
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $managerRoleId = DB::table('roles')->where('name', 'manager')->value('id');

    //    // إنشاء حساب الأدمن
    //     User::firstOrCreate(
    //         ['email' => 'admin@example.com'],
    //         [
    //             'password' => Hash::make('password123'),
    //             'role_id' => $adminRoleId,
    //         ]
    //     );

    //     // إنشاء حساب المدير
    //     User::firstOrCreate(
    //         ['email' => 'manager@example.com'],
    //         [
    //             'password' => Hash::make('password123'),
    //             'role_id' => $managerRoleId,
    //         ]
    //     );
     // إنشاء حساب admin إذا لم يكن موجود
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone_number' => '0911111111',
                'gender' => 'male',
                "birthday"=> "2005-06-15",
                'role_id' => $adminRoleId,
                'password' => Hash::make('password123'),
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // إنشاء حساب manager إذا لم يكن موجود
        DB::table('users')->updateOrInsert(
            ['email' => 'manager@example.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Manager',
                'phone_number' => '0922222222',
                'gender' => 'male',
                "birthday"=> "2005-06-15",
                'role_id' => $managerRoleId,
                'password' => Hash::make('password123'),
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
