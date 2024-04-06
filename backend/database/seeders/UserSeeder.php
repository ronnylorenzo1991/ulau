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
                'email'    => 'desarrollo@pcgeek.cl',
                'password' => 'secret',
                'roles'    => ['desarrollo'],
            ],
        ];

        foreach ($users as $key => $u) {
            $user = User::where('name', $u['name'])->first();
            if (empty($user)) {
                $user = User::create([
                    'name'      => $u['name'],
                    'email'     => $u['email'],
                    'password'  => Hash::make($u['password'])
                ]);
            }
            $user->syncRoles($u['roles']);
        }
    }
}
