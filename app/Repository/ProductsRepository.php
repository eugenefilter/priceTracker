<?php declare(strict_types=1);

namespace App\Repository;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductsRepository
{
  public function forUser(int $userId, int $perPage = 10): LengthAwarePaginator
  {
    return Product::query()
      ->where('user_id', $userId)
      ->latest()
      ->paginate($perPage);
  }

  public function forUserWithHistory(int $userId, int $perPage = 10): LengthAwarePaginator
  {
    return Product::query()
      ->where('user_id', $userId)
      ->with('histories')
      ->latest()
      ->paginate($perPage);
  }
}
