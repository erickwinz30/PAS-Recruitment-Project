<?php

namespace App;

use App\RequestApproval;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;


class Stock extends Model
{
  public $incrementing = false;
  protected $guarded = ['id'];
  protected $keyType = 'string';

  protected $fillable = [
    'name',
    'amount',
    'is_deleted',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->{$model->getKeyName()} = (string) Str::uuid();
    });
  }

  public function requestApprovals()
  {
    return $this->hasMany(RequestApproval::class);
  }
}
