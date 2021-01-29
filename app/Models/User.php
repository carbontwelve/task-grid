<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function workbooks(): HasMany
    {
        return $this->hasMany(Workbook::class, 'authored_by');
    }

    public function worksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class, 'authored_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'authored_by');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'authored_by');
    }

    public function providers():HasMany
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }
}
