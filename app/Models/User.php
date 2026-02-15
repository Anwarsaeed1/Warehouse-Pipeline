<?php

namespace App\Models;

use App\Enum\Global\ActiveTypeEnum;
use App\Scopes\User\UserScopes;
use App\Services\Global\UploadService;
use App\Trait\Global\CreatedByObserver;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, UserScopes, CreatedByObserver, Notifiable, HasApiTokens, HasRoles, InteractsWithSockets;

    protected string $guard_name = 'sanctum';

    public bool $inPermission = true;
    public array $basicOperations = ['create', 'update', 'delete'];
    public array $specialOperations = ['view-all', 'view-own', 'export','restore'];

    protected $fillable = ['name', 'email', 'phone', 'avatar', 'gender', 'password', 'otp', 'is_active', 'last_login', 'created_by'];

    protected $hidden = ['password', 'remember_token'];

    /*
     |--------------------------------------------------------------------------
     | Casts && Set Custom Attributes
     |--------------------------------------------------------------------------
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_active' => ActiveTypeEnum::class,
        ];
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: static fn($value) => UploadService::url($value)
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: static fn($value) => bcrypt($value),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations methods
    |--------------------------------------------------------------------------
    */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'created_by');
    }
}
