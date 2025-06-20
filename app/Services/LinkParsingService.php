<?php declare(strict_types=1);

namespace App\Services;

use App\Actions\UseCaseAction;
use App\Models\Link;
use FakeShop\Models\FakeProduct;
use Illuminate\Support\Str;

final class LinkParsingService
{

  public function __construct()
  {
  }

  public function handle(UseCaseAction $action): void
  {
    Link::query()
      ->orderBy('user_id')
      ->each(function (Link $link) use ($action) {
        $action->handle($link->user_id, $link->url);
      });
  }
}
