<?php declare(strict_types=1);

namespace App\Repository;

use App\Models\ProductHistory;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductsChangeRepository
{
  public function forUser(int $userId, int $perPage = 10): LengthAwarePaginator
  {
    return ProductHistory::query()
      ->where('user_id', $userId)
      ->with('product:id,title')
      ->latest()
      ->paginate($perPage);
  }
}
