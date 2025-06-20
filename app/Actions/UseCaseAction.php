<?php declare(strict_types=1);

namespace App\Actions;

use App\Models\Product;
use App\Models\ProductHistory;
use FakeShop\Models\FakeProduct;
use Illuminate\Support\Str;

final class UseCaseAction
{
  public function handle(int $userId, string $link): void
  {
    $fake = FakeProduct::query()
      ->where('slug', Str::afterLast($link, '/'))
      ->first();

    if (!$fake) {
      return;
    }

    $product = Product::query()
      ->firstOrNew([
        'url' => $link,
        'user_id' => $userId,
      ]);

    $priceChanged = $product->exists && ($product->price != $fake->price || $product->available !== $fake->available);

    if ($priceChanged) {
      ProductHistory::query()->create([
        'product_id' => $product->id,
        'user_id' => $userId,
        'old_price' => $product->price,
        'new_price' => $fake->price,
        'status' => $fake->available,
        'changed_at' => now(),
      ]);

      $product->price = $fake->price;
      $product->availability = $fake->available;
      $product->url = $link;
      $product->save();
    }

    $product->fill([
      'title' => $fake->title,
      'price' => $fake->price,
      'availability' => $fake->available,
    ]);

    $product->save();
  }
}
