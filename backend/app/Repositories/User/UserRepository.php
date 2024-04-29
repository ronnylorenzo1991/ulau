<?php

namespace App\Repositories\User;

use Illuminate\Support\Str;
use App\Mail\UserCreatedMail;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Illuminate\Support\Facades\Mail;

class UserRepository extends SharedRepositoryEloquent
{
    private User $entity;
    public function __construct(
        User $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function create($userData)
    {
        $randomPassword =  str::random(8);
        $userData['password'] = bcrypt($randomPassword);

        $user = $this->entity->create($userData);
        foreach ($userData['roles'] as $roleId) {
            $currentRole = Role::find($roleId);
            $user->assignRole($currentRole);
        }

        $data = [
            'name'     => $user->name,
            'username' => $user->email,
            'password' => $randomPassword,
        ];
        Mail::to($user->email)->send(new UserCreatedMail($data));
        return $user;
    }

    public function update($data, $id)
    {
        $user = $this->entity->find($id);

        $user->update($data);

        $user->syncRoles($data['roles']);
        return $user;
    }
}
