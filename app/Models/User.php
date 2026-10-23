<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'profile_picture',
        'last_log_by'
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        $roleIds = DB::table('role_users')
            ->where('user_id', $this->id)
            ->pluck('role_id');

        if ($roleIds->isEmpty()) {
            return [
                'read'   => false,
                'write'  => false,
                'create' => false,
                'delete' => false,
                'import' => false,
                'export' => false,
                'logs'   => false,
            ];
        }

        $permissions = DB::table('role_permissions')
            ->whereIn('role_id', $roleIds)
            ->where('navigation_menu_id', $navigationMenuId)
            ->selectRaw('
                MAX(read_access) as read_access,
                MAX(write_access) as write_access,
                MAX(create_access) as create_access,
                MAX(delete_access) as delete_access,
                MAX(import_access) as import_access,
                MAX(export_access) as export_access,
                MAX(logs_access) as logs_access
            ')
            ->first();

        return [
            'read'   => (bool) ($permissions->read_access ?? false),
            'write'  => (bool) ($permissions->write_access ?? false),
            'create' => (bool) ($permissions->create_access ?? false),
            'delete' => (bool) ($permissions->delete_access ?? false),
            'import' => (bool) ($permissions->import_access ?? false),
            'export' => (bool) ($permissions->export_access ?? false),
            'logs'   => (bool) ($permissions->logs_access ?? false),
        ];
    }

    protected function profilePictureUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->profile_picture ?? '';
                $defaultProfilePicture = asset('assets/media/default/default-avatar.jpg');

                return ($path !== '' && Storage::disk('public')->exists($path))
                    ? Storage::url($path)
                    : $defaultProfilePicture;
            }
        );
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}
