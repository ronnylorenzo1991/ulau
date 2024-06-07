<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = [
            [
                'name'     => 'Desarrollo',
                'phone'    => '555550',
                'password' => 'secret',
                'roles'    => ['desarrollo'],
            ],
            [
                'name'     => 'Fulanita',
                'phone'    => '555551',
                'password' => 'secret',
                'roles'    => ['cliente'],
            ],
            [
                'name'     => 'Menganita',
                'phone'    => '555552',
                'password' => 'secret',
                'roles'    => ['cliente'],
            ],
            [
                'name'     => 'Siclaneja',
                'phone'    => '555553',
                'password' => 'secret',
                'roles'    => ['cliente'],
            ],
        ];

        foreach ($users as $key => $u) {
            $user = User::where('name', $u['name'])->first();
            if (empty($user)) {
                $user = User::create([
                    'name'      => $u['name'],
                    'phone'     => $u['phone'],
                    'password'  => Hash::make($u['password'])
                ]);
            }
            $user->syncRoles($u['roles']);
        }
    }
}
