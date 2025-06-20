<?php declare(strict_types=1);

namespace FakeShop\Repositories;

use FakeShop\Models\FakeProduct;
use FakeShop\Request\PaginationRequest;
use Illuminate\Pagination\LengthAwarePaginator;

final class FakeProductRepository
{
  public function getAl(PaginationRequest $request): LengthAwarePaginator
  {
    return FakeProduct::query()
      ->orderBy($request->sortBy, $request->sortDir)
      ->paginate(
        perPage: $request->perPage,
        columns: ['id', 'title', 'slug', 'price', 'available', 'updated_at'],
        page: $request->page,
      );
  }
}
