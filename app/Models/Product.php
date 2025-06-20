<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
  protected $fillable = ['url', 'title', 'price', 'availability', 'user_id'];

  public function histories(): HasMany
  {
    return $this->hasMany(ProductHistory::class, 'product_id', 'id');
  }
}
