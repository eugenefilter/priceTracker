<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
  protected $fillable = ['url', 'user_id'];

}
