<?php declare(strict_types=1);

namespace FakeShop\Console;

use FakeShop\Models\FakeProduct;
use Illuminate\Console\Command;

final class UpdateFakeProductPrices extends Command
{
    protected $signature = 'fake-shop:update-prices';
    protected $description = 'Обновляет цены и наличие фейковых товаров';

    public function handle(): void
    {
        FakeProduct::all()->each(function ($product) {
            $oldPrice = $product->price;
            $delta = $oldPrice * (rand(-20, 20) / 100);
            $newPrice = max(1, round($oldPrice + $delta, 2));

            $product->update([
                'price' => $newPrice,
                'available' => (bool)rand(0, 1),
            ]);

            $this->info("{$product->title} — новая цена: {$newPrice}, наличие: " . ($product->available ? 'Да' : 'Нет'));
        });
    }
}
