<?php declare(strict_types=1);

namespace App\Repository;

use App\Models\Link;
use Illuminate\Pagination\LengthAwarePaginator;

final class LinkRepository
{
  public function forUser(int $userId): LengthAwarePaginator
  {
    return Link::query()
      ->where('user_id', $userId)
      ->latest()
      ->paginate(10);
  }
}
