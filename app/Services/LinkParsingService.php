<?php declare(strict_types=1);

namespace App\Services;

use App\Actions\UseCaseAction;
use App\Models\Link;

final class LinkParsingService
{

  public function __construct(private readonly UseCaseAction $action)
  {
  }

  public function handle(): void
  {
    $action = $this->action;
    Link::query()
      ->orderBy('user_id')
      ->each(function (Link $link) use ($action) {
        $action->handle($link->user_id, $link->url);
      });
  }
}
