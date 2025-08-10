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

       // إنشاء حساب الأدمن
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'password' => Hash::make('password123'),
                'role_id' => $adminRoleId,
            ]
        );

        // إنشاء حساب المدير
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'password' => Hash::make('password123'),
                'role_id' => $managerRoleId,
            ]
        );
    }
}
