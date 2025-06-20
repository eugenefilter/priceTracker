<?php declare(strict_types=1);

namespace FakeShop\Request;

use Illuminate\Http\Request;

final class PaginationRequest
{
  public function __construct(
    public readonly int     $page,
    public readonly int     $perPage = 20,
    public readonly ?string $sortBy = 'id',
    public readonly ?string $sortDir = 'asc',
  )
  {
  }

  public static function fromRequest(Request $request): self
  {
    return new self(
      (int)$request->get('page', 1),
      (int)$request->get('perPage', 20),
      $request->get('sortBy', 'id'),
      $request->get('sortDir', 'asc'),
    );
  }
}
