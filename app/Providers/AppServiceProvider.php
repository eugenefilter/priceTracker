<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WebSocket\WebSocketServer;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(WebSocketServer::class, function () {
      $host = env('WEBSOCKET_HOST', '127.0.0.1');
      $wsPort = (int)env('WEBSOCKET_PORT', 8443);
      $httpPort = (int)env('HTTP_PORT', 6001);
      $appKey = env('PUSHER_APP_KEY', '');
      $appSecret = env('PUSHER_APP_SECRET', '');

      return new WebSocketServer(
        host: $host,
        port: $wsPort,
        httpPort: $httpPort,
        appKey: $appKey,
        appSecret: $appSecret,
      );
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
