<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password', 'roles_id', 'center_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAllNavsAttribute()
    {
        $navs = Nav::with('navs')->whereHas('permissions', function ($query) {
            $query->whereIn('id', $this->getAllPermissions()->pluck('id')->toArray());
        })->get();

        return $navs;
    }

    public function getNavsAttribute()
    {
        $navs = Nav::with(['navs' => fn($query) => $query->whereHas('permissions', function ($query) {
            $query->whereIn('id', $this->getAllPermissions()->pluck('id')->toArray());
        })])->whereHas('permissions', function ($query) {
            $query->whereIn('id', $this->getAllPermissions()->pluck('id')->toArray());
        })->get();

        return $navs;
    }


    public function hasPermissionGroup($permission = "")
    {
        $permissions = auth()->user()->getAllPermissions();

        $hasAdminPermissions = $permissions->filter(function ($item) use ($permission) {
            return str_contains($item->name, $permission . '.');
        });

        return $hasAdminPermissions->count() > 0;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token, $this->getEmailForPasswordReset()));
    }
}
