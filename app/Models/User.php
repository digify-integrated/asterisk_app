<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'profile_picture',
        'last_log_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    public function getMenuPermissions(int $navigationMenuId): array
    {
        // 1. Fetch user roles directly via the role_users table mapping
        $roleIds = DB::table('role_users')
            ->where('user_id', $this->id)
            ->pluck('role_id');

        if ($roleIds->isEmpty()) {
            return [
                'read'   => false,
                'write'  => false,
                'create' => false,
                'delete' => false,
                'export' => false,
                'logs'   => false,
            ];
        }

        // 2. Aggregate across all assigned roles
        $permissions = DB::table('role_permissions')
            ->whereIn('role_id', $roleIds)
            ->where('navigation_menu_id', $navigationMenuId)
            ->selectRaw('
                MAX(read_access) as read_access,
                MAX(write_access) as write_access,
                MAX(create_access) as create_access,
                MAX(delete_access) as delete_access,
                MAX(export_access) as export_access,
                MAX(logs_access) as logs_access
            ')
            ->first();

        // 3. Return cleanly mapped boolean primitives
        return [
            'read'   => (bool) ($permissions->read_access ?? false),
            'write'  => (bool) ($permissions->write_access ?? false),
            'create' => (bool) ($permissions->create_access ?? false),
            'delete' => (bool) ($permissions->delete_access ?? false),
            'export' => (bool) ($permissions->export_access ?? false),
            'logs'   => (bool) ($permissions->logs_access ?? false),
        ];
    }
}
