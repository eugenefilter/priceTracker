<?php

use WebSocket\WebSocketServer;

require __DIR__ . '/vendor/autoload.php';

$host = env('WEBSOCKET_HOST', '127.0.0.1');
$wsPort = (int)env('WEBSOCKET_PORT', 8443);
$httpPort = (int)env('HTTP_PORT', 6001);
$appKey = env('PUSHER_APP_KEY', '');
$appSecret = env('PUSHER_APP_SECRET', '');

$server = new WebSocketServer(
  host: $host,
  port: $wsPort,
  httpPort: $httpPort,
  appKey: $appKey,
  appSecret: $appSecret,
);
$server->start();
