<?php declare(strict_types=1);

namespace FakeShop\Providers;

use FakeShop\Console\LoopFakeProductUpdater;
use FakeShop\Console\UpdateFakeProductPrices;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

final class FakeShopServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

//    $this->loadViewsFrom(__DIR__ . '/../views', 'fake-shop');

    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }

  public function register(): void
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        UpdateFakeProductPrices::class,
        LoopFakeProductUpdater::class,
      ]);
    }
  }
}
