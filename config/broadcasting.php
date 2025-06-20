<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Default Broadcaster
  |--------------------------------------------------------------------------
  |
  | Этот драйвер используется, когда вы вызываете event(new ...) без
  | явной привязки к каналу.
  |
  */

  'default' => env('BROADCAST_DRIVER', 'null'),


  /*
  |--------------------------------------------------------------------------
  | Broadcast Connections
  |--------------------------------------------------------------------------
  |
  | Здесь настраиваются все «каналы» (drivers), которые умеют отдавать
  | события клиенту.
  |
  */

  'connections' => [

    'pusher' => [
      'driver' => 'pusher',
      'key' => env('PUSHER_APP_KEY'),
      'secret' => env('PUSHER_APP_SECRET'),
      'app_id' => env('PUSHER_APP_ID'),
      'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
        'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        'host' => env('PUSHER_HOST', '127.0.0.1'),
        'port' => env('PUSHER_PORT', 6001),
        'scheme' => env('PUSHER_SCHEME', 'http'),
        'encrypted' => env('PUSHER_SCHEME', 'https') === 'https',

        // Добавляем параметры для отладки cURL
        'curl_options' => [
          CURLOPT_VERBOSE => true,
          CURLOPT_HEADER => true,
        ],
      ],
    ],

    'redis' => [
      'driver' => 'redis',
      'connection' => env('BROADCAST_REDIS_CONNECTION', 'default'),
    ],

    'log' => [
      'driver' => 'log',
    ],

    'null' => [
      'driver' => 'null',
    ],

  ],

];
