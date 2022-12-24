<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Uuid;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use Recordable;
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'name_kana',
        'email',
        'code',
        'password',
        'avatar',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'avatar',
        'password',
        'remember_token',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_super_admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'avatar_name'];

    /**
     * get absolute url
     */
    public function getAvatarUrlAttribute()
    {
        return is_null($this->avatar) ? null : Storage::url($this->avatar);
    }
    /**
     * get image name
     */
    public function getAvatarNameAttribute()
    {
        return is_null($this->avatar) ? '' : Str::afterLast($this->avatar, '/');
    }

    /**
     * Relationship form user with user_companies table
     * In an organization, user can only get 1 company
     *
     * @return mixed
     */
    public function getCompanyAttribute()
    {
        return $this->companies->first();
    }

    /**
     * Check user is supper admin
     *
     * @return bool
     */
    public function isSupperAdmin()
    {
        return $this->is_super_admin;
    }

    /**
     * Check user is administrator of organization
     * Check via request middleware
     *
     * @return bool
     */
    public function isAdmin()
    {
        try {
            return app('request')->get('user_role') == ADMIN_ROLE;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check user is admin or supper admin
     *
     * @return bool
     */
    public function isAdminHigher()
    {
        return $this->isSupperAdmin() || $this->isAdmin();
    }

    /**
     * @param $builder
     * @param $organId
     *
     * @return mixed
     */
    public function scopeWithCompany($builder, $organId)
    {
        return $builder
            ->with(['companies' => function ($q) use ($organId) {
                $q->ofOrgan($organId);
            }]);
    }

    /**
     * @param $builder
     * @param $organId
     *
     * @return mixed
     */
    public function scopeWithDepartment($builder, $organId)
    {
        return $builder
            ->with(['departments' => function ($q) use ($organId) {
                $q->ofOrgan($organId);
            }]);
    }

    /**
     * @param $builder
     * @param $organId
     *
     * @return mixed
     */
    public function scopeWithOrganization($builder, $organId)
    {
        return $builder
            ->with(['organizations' => function ($q) use ($organId) {
                $q->where('organizations.id', $organId);
            }]);
    }

    /**
     * Relationship form user with roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'id');
    }

    /**
     * Relationship with companies of user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, UserCompany::class)->withTimestamps();
    }

    /**
     * Relationship with organization of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, UserRole::class)
            ->withTimestamps()
            ->withPivot('role');
    }
    /**
     * Relationship with departments of user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, UserDepartment::class, 'user_id', 'department_id')
            ->withPivot(['user_disp_order', 'organization_id'])
            ->withTimestamps();
    }

    /**
     * Get news by relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function news()
    {
        return $this->belongsToMany(News::class, NewsResponse::class, 'user_id', 'news_id');
    }

    /**
     * User's News responses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newsResponses()
    {
        return $this->hasMany(NewsResponse::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notifyGroups()
    {
        return $this->belongsToMany(NotifyRecipientGroup::class, NotifyRecipient::class, 'user_id', 'group_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
