<?php

namespace App;

use App\User;
use App\Stock;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class RequestApproval extends Model
{
  public $incrementing = false;
  protected $guarded = ['id'];
  protected $keyType = 'string';

  protected $fillable = [
    'user_id',
    'stock_id',
    'amount',
    'is_entry',
    'status',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->{$model->getKeyName()} = (string) Str::uuid();
    });
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function stock()
  {
    return $this->belongsTo(Stock::class);
  }
}
