<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductHistory extends Model
{
  protected $fillable = [
    'product_id',
    'user_id',
    'old_price',
    'new_price',
    'changed_at',
  ];

  public $timestamps = false;

  protected $casts = [
    'changed_at' => 'datetime',
  ];

  public function product(): HasOne
  {
    return $this->hasOne(Product::class, 'id', 'product_id');
  }
}
