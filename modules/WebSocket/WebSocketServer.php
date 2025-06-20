<?php declare(strict_types=1);

namespace WebSocket;

use Random\RandomException;
use RuntimeException;

final class WebSocketServer
{
  use MaskData;

  private $socket;
  private $httpSocket;
  private array $clients = [];
  private array $channelSubscriptions = [];

  public function __construct(
    private readonly string $host,
    private readonly int    $port,
    private readonly int    $httpPort,
    private readonly string $appKey,
    private readonly string $appSecret,
  )
  {
  }

  /**
   * Запускает WebSocket и HTTP-сервер.
   */
  public function start(): void
  {
    $this->setupMasterSocket();
    $this->setupHttpSocket();

    echo "WebSocket server started on {$this->host}:{$this->port}\n";
    echo "HTTP API listening on {$this->host}:{$this->httpPort}\n";
    echo "Waiting for connections...\n";

    while (true) {
      $read = array_merge(
        [$this->socket, $this->httpSocket],
        array_values($this->clients)
      );
      $write = null;
      $except = null;

      if (empty($read) || socket_select($read, $write, $except, 1) < 1) {
        continue;
      }

      foreach ($read as $socket) {
        if ($socket === $this->socket) {
          $this->handleNewConnection();
        } elseif ($socket === $this->httpSocket) {
          echo "WebSocket server started on handleHttpConnection\n";
          $this->handleHttpConnection();
        } else {
          $this->handleClientData($socket);
        }
      }
    }
  }

  private function setupMasterSocket(): void
  {
    $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($this->socket === false) {
      throw new RuntimeException("Failed to create socket: " . socket_strerror(socket_last_error()));
    }
    socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($this->socket, $this->host, $this->port);
    socket_listen($this->socket);
  }

  private function setupHttpSocket(): void
  {
    $this->httpSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($this->httpSocket, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($this->httpSocket, $this->host, $this->httpPort);
    socket_listen($this->httpSocket);
  }

  private function handleNewConnection(): void
  {
    $newClient = socket_accept($this->socket);
    if ($newClient === false) {
      error_log("Failed to accept new client connection: " . socket_strerror(socket_last_error($this->socket)));
      return;
    }

    if (!$this->performHandshake($newClient)) {
      socket_close($newClient);
      error_log("WebSocket handshake failed for new client.");
      return;
    }

    $receivedMessage = $this->handleClientData($newClient);
    if (json_validate($receivedMessage)) {
      $data = json_decode($receivedMessage, true);
      if (isset($data['user_id'])) {
        $this->addClient((int)$data['user_id'], $newClient);
        socket_getpeername($newClient, $ip, $port);
        echo "Client connected: {$ip}:{$port}\n";
        return;
      }
    }

    $this->removeClient($newClient);
  }

  private function handleHttpConnection(): void
  {
    // Пришёл новый HTTP-коннект от Laravel/Pusher-клиента
    echo "[HTTP] Connection received on {$this->host}:{$this->httpPort}\n";

    $conn = socket_accept($this->httpSocket);
    if ($conn === false) {
      error_log("[HTTP] Failed to accept connection: " . socket_strerror(socket_last_error($this->httpSocket)));
      return;
    }

    // Читаем заголовки до первой пустой строки
    $request = '';
    while (strpos($request, "\r\n\r\n") === false) {
      $chunk = socket_read($conn, 1024);
      if ($chunk === false || $chunk === '') {
        socket_close($conn);
        return;
      }
      $request .= $chunk;
    }

    // Разделяем заголовки и тело
    list($head, $body) = explode("\r\n\r\n", $request, 2);
    $lines = explode("\r\n", $head);

    // Разбираем первую строку: метод и путь
    [$method, $path] = explode(' ', $lines[0], 3);
    echo "[HTTP] {$method} {$path}\n";

    // Если это нужный нам POST /apps/{app_id}/events
    if ($method === 'POST' && preg_match('#^/apps/[^/]+/events#', $path)) {
      // Ищем Content-Length, чтобы докачать тело
      $length = 0;
      foreach ($lines as $h) {
        if (stripos($h, 'Content-Length:') === 0) {
          $length = (int)trim(substr($h, 15));
          break;
        }
      }
      // Докачиваем оставшиеся байты тела
      while (strlen($body) < $length) {
        $chunk = socket_read($conn, $length - strlen($body));
        if ($chunk === false) break;
        $body .= $chunk;
      }

      echo "[HTTP] Body: {$body}\n";

      // Парсим JSON и шлем дальше
      $payload = json_decode($body, true);
      if (is_array($payload)) {
        $this->dispatchPusherEvent($payload);
      } else {
        error_log("[HTTP] Invalid JSON payload");
      }
    }

    // Формируем ответ с корректным JSON-телом
    $responseBody = json_encode(['status' => 'ok']);
    $headers =
      "HTTP/1.1 200 OK\r\n" .
      "Content-Type: application/json\r\n" .
      "Content-Length: " . strlen($responseBody) . "\r\n" .
      "Connection: close\r\n\r\n";

    // Отправляем заголовки + тело
    socket_write($conn, $headers . $responseBody);
    socket_close($conn);

    echo "[HTTP] Responded with 200 OK and JSON\n";
  }

  private function dispatchPusherEvent(array $payload): void
  {
    $raw = $payload['name'] ?? '';
    $eventName = str_contains($raw, '\\')
      ? substr($raw, (int)strrpos($raw, '\\') + 1)
      : $raw;

    // Pusher может прислать либо 'channels' => ['…'], либо 'channel' => '…'
    $channels = [];
    if (!empty($payload['channels']) && is_array($payload['channels'])) {
      $channels = $payload['channels'];
    } elseif (!empty($payload['channel'])) {
      $channels = [$payload['channel']];
    }

    // Данные могут быть уже массивом, а могут прийти строкой JSON
    $data = $payload['data'] ?? [];
    if (is_string($data)) {
      $decoded = json_decode($data, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        $data = $decoded;
      }
    }

    echo "[DISPATCH] event={$eventName} channels=[" . implode(',', $channels) . "]\n";
    echo "[DISPATCH] data=" . json_encode($data) . "\n";

    foreach ($channels as $chan) {
      if (empty($this->channelSubscriptions[$chan])) {
        continue;
      }

      foreach ($this->channelSubscriptions[$chan] as $userId => $socket) {
        $msg = [
          'event' => $payload['name'],
          'channel' => $chan,
          'data' => $data,
        ];

        // Логируем отправку
        echo "[SEND] to user {$userId} on channel '{$chan}': " . json_encode($msg) . "\n";

        $this->sendData($socket, json_encode($msg));
      }
    }
  }

  private function handleClientData($client): string
  {
    $buffer = '';
    $bytes = @socket_recv($client, $buffer, 2048, 0);
    if ($bytes === 0 || $bytes === false) {
      $this->removeClient($client);
      return '';
    }

    $receivedMessage = $this->unmaskData($buffer);
    socket_getpeername($client, $ip, $port);
    echo "Message from {$ip}:{$port}: {$receivedMessage}\n";

    if (json_validate($receivedMessage)) {
      $msgData = json_decode($receivedMessage, true);
      if (isset($msgData['command'])) {
        $this->commands($msgData['command'], $client);
      }
      if (isset($msgData['event']) && $msgData['event'] === 'pusher:subscribe') {
        $chan = $msgData['data']['channel'] ?? '';
        $uid = array_search($client, $this->clients, true);
        if ($chan !== '' && $uid !== false) {
          $this->channelSubscriptions[$chan][$uid] = $client;
          echo "[SUBSCRIBE] user {$uid} -> channel '{$chan}'\n";
        }
        $ack = [
          'event' => 'pusher_internal:subscription_succeeded',
          'channel' => $chan,
          'data' => (object)[],
        ];
        $this->sendData($client, json_encode($ack));
        return '';
      }
    }

    return $receivedMessage;
  }

  private function commands(string $command, $client): void
  {
    echo "Command: {$command}\n";
    switch ($command) {
      case 'get_connected_users':
        $response = [
          'type' => 'connected_users_list',
          'users' => $this->getConnectedUserIds(),
        ];
        $this->sendData($client, json_encode($response));
        break;
    }
  }

  private function addClient(int $id, $socket): void
  {
    $this->clients[$id] = $socket;
  }

  private function removeClient($socket): void
  {
    $key = array_search($socket, $this->clients, true);
    if ($key !== false) {
      unset($this->clients[$key]);
      socket_getpeername($socket, $ip, $port);
      echo "Client disconnected: {$ip}:{$port}\n";
    } else {
      socket_getpeername($socket, $ip, $port);
      echo "Client (unknown user ID) disconnected: {$ip}:{$port}\n";
    }
    socket_close($socket);
  }

  private function performHandshake($socket): bool
  {
    $request = socket_read($socket, 2048);
    if ($request === false) {
      return false;
    }
    if (!preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches)) {
      return false;
    }
    $key = trim($matches[1]);
    $acceptKey = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $upgrade =
      "HTTP/1.1 101 Switching Protocols\r\n" .
      "Upgrade: websocket\r\n" .
      "Connection: Upgrade\r\n" .
      "Sec-WebSocket-Accept: {$acceptKey}\r\n\r\n";
    return socket_write($socket, $upgrade, strlen($upgrade)) > 0;
  }

  /**
   * @throws RandomException
   */
  public function sendData($clientSocket, string $message): void
  {
    $masked = $this->maskData($message);
    socket_write($clientSocket, $masked, strlen($masked));
  }

  public function getConnectedUserIds(): array
  {
    return array_keys($this->clients);
  }

  public function sendJsonToUser(int $userId, array $data): bool
  {
    $client = $this->clients[$userId] ?? null;
    if (!$client) {
      return false;
    }
    $json = json_encode($data);
    if ($json === false) {
      return false;
    }
    $this->sendData($client, $json);
    return true;
  }

  public function getClientSocket(int $userId)
  {
    return $this->clients[$userId] ?? null;
  }
}
