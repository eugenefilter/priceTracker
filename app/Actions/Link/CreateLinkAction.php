<?php declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use Illuminate\Validation\ValidationException;

final class CreateLinkAction
{
  /**
   * @throws ValidationException
   */
  public function handle(string $url, int $userId): Link
  {
    $exists = Link::query()
      ->where('user_id', $userId)
      ->where('url', $url)
      ->exists();

    if ($exists) {
      throw ValidationException::withMessages([
        'url' => 'Вы уже добавили эту ссылку.',
      ]);
    }

    return Link::query()->create([
      'user_id' => $userId,
      'url' => $url,
    ]);
  }
}
