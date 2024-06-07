<?php

namespace App\Http\Controllers;

use App\Models\Nav;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    public function getLists(Request $request)
    {
        $request->validate([
            'lists' => 'required',
        ]);


        $results = [];

        foreach (json_decode($request->get('lists')) as $item) {
            switch ($item) {
                case 'roles':
                    $results['roles'] = Role::all()->pluck('name', 'id');
                    break;
                case 'navs':
                    $results['navs'] = Nav::all()->whereNull('nav_id')->pluck('name', 'id');
                    break;
                case 'clients':
                    $results['clients'] = User::whereHas("roles", function ($q) {
                        $q->where("name", "cliente");
                    })->get()->pluck('name', 'id');
                    break;
                case 'permissions':
                    $results['permissions'] = Permission::all();
                    break;
                case 'permissionList':
                    $results['permissionList'] = Permission::all()->pluck('name', 'id');
                    break;
            }
        }

        return json_encode(['lists' => $results]);
    }
}
