<?php declare(strict_types=1);

namespace FakeShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class FakeProduct extends Model
{
  use HasFactory;

  protected $fillable = ['title', 'slug', 'price', 'available'];
}
