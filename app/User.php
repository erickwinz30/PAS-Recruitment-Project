<?php

namespace App;

use App\RequestApproval;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  public $incrementing = false;
  protected $guarded = ['id'];
  protected $keyType = 'string';

  protected $fillable = [
    'name',
    'email',
    'phone_number',
    'telegram_chat_id',
    'is_admin',
    'is_deleted',
    'password',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->{$model->getKeyName()} = (string) Str::uuid();
    });
  }

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

  public function requestApprovals()
  {
    return $this->hasMany(RequestApproval::class);
  }
}
